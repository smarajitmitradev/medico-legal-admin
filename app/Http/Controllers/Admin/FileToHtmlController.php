<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\ListItem;
use PhpOffice\PhpWord\Element\ListItemRun;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Title;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\ListItem as ListItemStyle;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Http;

class FileToHtmlController extends Controller
{
    /** Running counter for numbered section headings extracted from lists */
    private int $sectionCounter = 0;

    public function convert(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $html = $this->extractHtml($file, $extension);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not convert file: ' . $e->getMessage()], 422);
        }

        if (trim($html) === '') {
            return response()->json(['error' => 'File appears to be empty or unsupported.'], 422);
        }

        $html = preg_replace('/\r\n|\r|\n/', '', $html);
        $html = preg_replace('/>\s+</', '><', trim($html));

        return response()->json(['html' => $html]);
    }

    private function extractHtml($file, string $extension): string
    {
        $path = $file->getRealPath();

        switch ($extension) {
            case 'docx':
            case 'doc':
                return $this->docxToHtml($path);

            case 'pdf':
                return $this->pdfToHtml($path);

            case 'html':
            case 'htm':
                return file_get_contents($path);

            case 'txt':
            case 'md':
            case 'csv':
            case 'json':
                return '<pre>' . htmlspecialchars(file_get_contents($path)) . '</pre>';

            default:
                throw new \RuntimeException('Unsupported file type: ' . $extension);
        }
    }

    /**
     * Convert PDF -> DOCX (via LibreOffice headless) -> HTML, reusing the
     * exact same PhpWord walker used for native .docx uploads.
     */
    private function pdfToHtml(string $pdfPath): string
{
    $apiKey = env('CLOUDCONVERT_API_KEY');
    if (!$apiKey) {
        throw new \RuntimeException('CLOUDCONVERT_API_KEY is not set in .env');
    }

    $workDir = $this->makeTempDir('pdf2docx_');

    try {
        // 1. Create a job: import (upload) -> convert -> export (url)
        $jobResponse = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.cloudconvert.com/v2/jobs', [
                'tasks' => [
                    'import-file' => [
                        'operation' => 'import/upload',
                    ],
                    'convert-file' => [
                        'operation' => 'convert',
                        'input' => 'import-file',
                        'input_format' => 'pdf',
                        'output_format' => 'docx',
                    ],
                    'export-file' => [
                        'operation' => 'export/url',
                        'input' => 'convert-file',
                    ],
                ],
            ]);

        if ($jobResponse->failed()) {
            throw new \RuntimeException('CloudConvert job creation failed: ' . $jobResponse->body());
        }

        $job = $jobResponse->json('data');
        $importTask = collect($job['tasks'])->firstWhere('name', 'import-file');
        $uploadUrl = $importTask['result']['form']['url'];
        $uploadParams = $importTask['result']['form']['parameters'];

        // 2. Upload the actual PDF bytes to the upload URL CloudConvert gave us
        $uploadResponse = Http::timeout(60)
        ->attach('file', file_get_contents($pdfPath), 'source.pdf')
        ->post($uploadUrl, $uploadParams);

        if ($uploadResponse->failed()) {
            throw new \RuntimeException('CloudConvert file upload failed: ' . $uploadResponse->body());
        }

        // 3. Poll job status until finished (or failed/timeout)
        $jobId = $job['id'];
        $exportUrl = null;
        $maxAttempts = 30; // ~30 * 2s = 60s max wait

        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(2);

            $statusResponse = Http::withToken($apiKey)
                ->timeout(30)
                ->get("https://api.cloudconvert.com/v2/jobs/{$jobId}");

            $statusJob = $statusResponse->json('data');

            if ($statusJob['status'] === 'error') {
                throw new \RuntimeException('CloudConvert job failed: ' . json_encode($statusJob));
            }

            if ($statusJob['status'] === 'finished') {
                $exportTask = collect($statusJob['tasks'])->firstWhere('name', 'export-file');
                $exportUrl = $exportTask['result']['files'][0]['url'] ?? null;
                break;
            }
        }

        if (!$exportUrl) {
            throw new \RuntimeException('CloudConvert conversion timed out.');
        }

        // 4. Download the converted DOCX
        $docxPath = $workDir . DIRECTORY_SEPARATOR . 'converted.docx';
        $fileResponse = Http::timeout(60)->get($exportUrl);
        file_put_contents($docxPath, $fileResponse->body());

        // 5. Reuse the exact same walker used for native .docx uploads
        return $this->docxToHtml($docxPath);
    } finally {
        $this->cleanupDir($workDir);
    }
}

    /**
     * Runs `soffice --headless --convert-to <targetFormat>` with an isolated
     * user profile per call (avoids the shared-profile lock that causes
     * failures when two conversions run at the same time on a live server).
     */
    private function runSoffice(string $workDir, string $inputPath, string $targetFormat): void
    {
        $sofficeBinary = $this->resolveSofficeBinary();

        $profileDir = $workDir . DIRECTORY_SEPARATOR . 'profile';
        mkdir($profileDir, 0777, true);
        // LibreOffice's -env: flag needs a file:// URL with forward slashes,
        // even on Windows.
        $profileUrl = 'file:///' . str_replace('\\', '/', $profileDir);

        $process = new Process([
            $sofficeBinary,
            '--headless',
            '--norestore',
            '-env:UserInstallation=' . $profileUrl,
            '--convert-to', $targetFormat,
            '--outdir', $workDir,
            $inputPath,
        ]);
        $process->setTimeout(90);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(
                'LibreOffice conversion failed: ' . ($process->getErrorOutput() ?: $process->getOutput())
            );
        }
    }

    /**
     * Resolves the LibreOffice binary path across environments:
     *   1. Explicit override via SOFFICE_BINARY in .env (recommended for
     *      any production server — set it once, works everywhere).
     *   2. Common Windows install locations (local XAMPP dev).
     *   3. Common Linux install locations (covers most VPS/Hostinger setups
     *      where "soffice" may or may not be on PHP-FPM's PATH).
     *   4. Falls back to bare "soffice", assuming it's on PATH.
     */
    private function resolveSofficeBinary(): string
    {
        if ($configured = env('SOFFICE_BINARY')) {
            if (!file_exists($configured) && stripos(PHP_OS, 'WIN') !== 0) {
                // On Linux a plain command name (not a path) is valid too —
                // only validate existence for absolute paths.
                if (str_starts_with($configured, '/')) {
                    throw new \RuntimeException("SOFFICE_BINARY is set to '{$configured}' but that file does not exist.");
                }
            }
            return $configured;
        }

        if (stripos(PHP_OS, 'WIN') === 0) {
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            ];
        } else {
            $candidates = [
                '/usr/bin/soffice',
                '/usr/lib/libreoffice/program/soffice',
                '/opt/libreoffice/program/soffice',
                '/snap/bin/libreoffice.soffice',
            ];
        }

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Last resort: assume it's resolvable via PATH
        return 'soffice';
    }

    private function makeTempDir(string $prefix): string
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $prefix . uniqid();
        mkdir($dir, 0777, true);
        return $dir;
    }

    private function cleanupDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? @rmdir($item->getRealPath()) : @unlink($item->getRealPath());
        }

        @rmdir($dir);
    }

    /**
     * Convert a .docx to HTML by walking PhpWord's element tree ourselves.
     * We do NOT use PhpWord's built-in HTML Writer for the body — that
     * writer frequently drops <ul>/<ol>/<li> markup for list paragraphs
     * (it renders them as plain <p> with no visible bullet). Walking the
     * tree directly lets us detect ListItem/ListItemRun elements and emit
     * real <ul>/<ol><li> ourselves, with the correct bullet/number style.
     */
    private function docxToHtml(string $path): string
    {
        $this->sectionCounter = 0; // reset per document

        $phpWord = IOFactory::load($path);
        $html = '';

        foreach ($phpWord->getSections() as $section) {
            $html .= $this->renderElements($section->getElements());
        }

        return $html;
    }

    /**
     * @param \PhpOffice\PhpWord\Element\AbstractElement[] $elements
     */
    private function renderElements(array $elements): string
    {
        $html = '';
        $i = 0;
        $count = count($elements);

        while ($i < $count) {
            $element = $elements[$i];

            if ($element instanceof ListItem || $element instanceof ListItemRun) {
                // Check if this "list item" is actually a heading in disguise
                // (some conversion engines, e.g. PDF->DOCX via CloudConvert,
                // represent section headings as level-0 numbered list items
                // rather than real Heading paragraph styles).
                $headingLevel = $this->detectHeadingLevel($element, true);

                if ($headingLevel !== null) {
                    $text = $this->extractRunText($element);
                    if (trim(strip_tags($text)) !== '') {
                        $depth = method_exists($element, 'getDepth') ? (int) $element->getDepth() : 0;
            
                        // The "1.", "2." prefix is auto-generated by Word's numbering
                        // definition, not stored as literal text — since we're pulling
                        // this out of the <ol> that would've rendered it via CSS, we
                        // have to generate the number ourselves. Only top-level (depth 0)
                        // sections get numbered this way; nested sub-bullets don't.
                        $prefix = '';
                        if ($depth === 0 && !preg_match('/^\d/', trim(strip_tags($text)))) {
                            $this->sectionCounter++;
                            $prefix = $this->sectionCounter . '. ';
                        }
            
                        $html .= "<h{$headingLevel}>{$prefix}{$text}</h{$headingLevel}>";
                    }
                    $i++;
                    continue;
                }

                // Collect this run of consecutive real list items (same list
                // block) into one <ul>/<ol>, tracking depth for nested lists.
                $listItems = [];
                while ($i < $count && ($elements[$i] instanceof ListItem || $elements[$i] instanceof ListItemRun)) {
                    // Stop grouping if we hit another heading-disguised item
                    if ($this->detectHeadingLevel($elements[$i], true) !== null) {
                        break;
                    }
                    $listItems[] = $elements[$i];
                    $i++;
                }
                $html .= $this->renderListBlock($listItems);
                continue;
            }

            $html .= $this->renderSingleElement($element);
            $i++;
        }

        return $html;
    }

/**
 * Returns a heading level (1-6) if this run should be rendered as a
 * heading, or null if it's a normal paragraph/list item.
 *
 * $isListItem: when true, we're being called on a ListItem/ListItemRun
 * that the list-grouping logic is deciding whether to pull out as a
 * heading instead of a bullet — in that case we require BOTH the
 * "looks like a heading" heuristic AND a numbered-section pattern
 * ("1. Introduction", "2.1 Purpose") since plain bullets can also be
 * short and bold, and we don't want to misclassify real list content.
 */

    /**
     * @param array<ListItem|ListItemRun> $listItems
     */
    private function renderListBlock(array $listItems): string
    {
        $html = '';
        $currentDepth = -1;
        $tagStack = [];

        foreach ($listItems as $item) {
            $depth = method_exists($item, 'getDepth') ? (int) $item->getDepth() : 0;
            $isOrdered = $this->isOrderedList($item);
            $tag = $isOrdered ? 'ol' : 'ul';

            if ($depth > $currentDepth) {
                for ($d = $currentDepth + 1; $d <= $depth; $d++) {
                    $style = $isOrdered
                        ? 'list-style-type:decimal;margin:0 0 6px 0;padding-left:20px;'
                        : 'list-style-type:disc;margin:0 0 6px 0;padding-left:20px;';
                    $html .= "<{$tag} style=\"{$style}\">";
                    $tagStack[] = $tag;
                }
            } elseif ($depth < $currentDepth) {
                for ($d = $currentDepth; $d > $depth; $d--) {
                    $closeTag = array_pop($tagStack) ?: $tag;
                    $html .= "</{$closeTag}>";
                }
            }

            $currentDepth = $depth;

            $text = $this->extractRunText($item);
            $html .= '<li style="display:list-item;">' . $text . '</li>';
        }

        while ($tagStack) {
            $closeTag = array_pop($tagStack);
            $html .= "</{$closeTag}>";
        }

        return $html;
    }

    private function isOrderedList($item): bool
    {
        $style = method_exists($item, 'getStyle') ? $item->getStyle() : null;

        if ($style instanceof ListItemStyle) {
            $numFormat = method_exists($style, 'getNumFormat') ? $style->getNumFormat() : null;
            if ($numFormat && in_array($numFormat, ['decimal', 'decimalZero', 'upperRoman', 'lowerRoman', 'upperLetter', 'lowerLetter'], true)) {
                return true;
            }
        }

        return false;
    }

    private function renderSingleElement($element): string
    {
        if ($element instanceof Title) {
            $depth = min(max((int) $element->getDepth(), 1), 6);
            $text = $this->extractTitleText($element);
            if (trim(strip_tags($text)) === '') {
                return '';
            }
            return "<h{$depth}>{$text}</h{$depth}>";
        }

        if ($element instanceof Table) {
            return $this->renderTable($element);
        }

        if ($element instanceof TextRun) {
            $headingLevel = $this->detectHeadingLevel($element);

            if ($headingLevel !== null) {
                $text = $this->extractRunText($element);
                if (trim(strip_tags($text)) === '') {
                    return '';
                }
                return "<h{$headingLevel}>{$text}</h{$headingLevel}>";
            }

            $text = $this->extractRunText($element);
            if (trim(strip_tags($text)) === '') {
                return '';
            }
            return '<p>' . $text . '</p>';
        }

        if ($element instanceof Text) {
            $text = htmlspecialchars($element->getText());
            if (trim($text) === '') {
                return '';
            }
            return '<p>' . $text . '</p>';
        }

        if ($element instanceof AbstractContainer) {
            return $this->renderElements($element->getElements());
        }

        return '';
    }

    private function extractTitleText(Title $element): string
    {
        if (!method_exists($element, 'getText')) {
            return '';
        }

        $text = $element->getText();

        if (is_string($text)) {
            return htmlspecialchars($text);
        }

        if (is_object($text) && method_exists($text, 'getElements')) {
            return $this->extractRunText($text);
        }

        return '';
    }

    private function detectHeadingLevel($run, bool $isListItem = false): ?int
    {
        $styleName = null;
        $pStyle = method_exists($run, 'getParagraphStyle') ? $run->getParagraphStyle() : null;

        if (is_string($pStyle)) {
            $styleName = $pStyle;
        } elseif (is_object($pStyle) && method_exists($pStyle, 'getStyleName')) {
            $styleName = $pStyle->getStyleName();
        }

        if ($styleName && preg_match('/heading\s*(\d)/i', $styleName, $m)) {
            return min(max((int) $m[1], 1), 6);
        }

        $text = trim(strip_tags($this->extractRunText($run)));
        if ($text === '') {
            return null;
        }

        $allBold = true;
        $maxFontSize = 0;
        $hasContent = false;

        $children = method_exists($run, 'getElements') ? $run->getElements() : [];

        foreach ($children as $child) {
            if (!($child instanceof Text)) {
                continue;
            }
            $childText = trim($child->getText());
            if ($childText === '') {
                continue;
            }
            $hasContent = true;

            $font = method_exists($child, 'getFontStyle') ? $child->getFontStyle() : null;
            $isBold = is_object($font) && method_exists($font, 'isBold') && $font->isBold();
            if (!$isBold) {
                $allBold = false;
            }
            if (is_object($font) && method_exists($font, 'getSize') && $font->getSize()) {
                $maxFontSize = max($maxFontSize, (float) $font->getSize());
            }
        }

        $hasNumberedPattern = preg_match('/^\d+(\.\d+)*\.?\s+\S/', $text);

        $looksLikeHeading = $hasContent
            && $allBold
            && mb_strlen($text) <= 100
            && !preg_match('/[.!?,;:]$/', $text);

        if ($isListItem) {
            // Stricter: a "list item" only counts as a heading if it ALSO
            // matches the numbered-section pattern — avoids misclassifying
            // genuinely bold, short bullet points as headings.
            return ($looksLikeHeading && $hasNumberedPattern) ? 2 : null;
        }

        if ($looksLikeHeading) {
            if ($hasNumberedPattern) {
                return 2;
            }
            if ($maxFontSize >= 16) {
                return 1;
            }
            return 2;
        }

        return null;
    }

    private function renderTable(Table $table): string
    {
        $html = '<table style="border-collapse:collapse;width:100%;margin-bottom:10px;">';

        foreach ($table->getRows() as $row) {
            $html .= '<tr>';
            foreach ($row->getCells() as $cell) {
                $cellHtml = $this->renderElements($cell->getElements());
                $html .= '<td style="border:1px solid #ccc;padding:6px;">' . $cellHtml . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    private function extractRunText($run): string
    {
        if (!method_exists($run, 'getElements')) {
            return method_exists($run, 'getText') ? htmlspecialchars((string) $run->getText()) : '';
        }

        $html = '';

        foreach ($run->getElements() as $child) {
            if ($child instanceof Text) {
                $rawText = $child->getText();

                // Skip completely empty runs (formatting artifacts from the
                // conversion) — these are what caused stray <strong></strong>
                // and <h1></h1> with no content.
                if ($rawText === '') {
                    continue;
                }

                $text = htmlspecialchars($rawText);
                $font = method_exists($child, 'getFontStyle') ? $child->getFontStyle() : null;

                if (is_object($font)) {
                    if (method_exists($font, 'isBold') && $font->isBold()) {
                        $text = "<strong>{$text}</strong>";
                    }
                    if (method_exists($font, 'isItalic') && $font->isItalic()) {
                        $text = "<em>{$text}</em>";
                    }
                    if (method_exists($font, 'getUnderline') && $font->getUnderline() && $font->getUnderline() !== 'none') {
                        $text = "<u>{$text}</u>";
                    }
                }

                $html .= $text;
            } elseif (method_exists($child, 'getText') && $child->getText() !== '') {
                $html .= htmlspecialchars((string) $child->getText());
            }
        }

        return $html;
    }

    private function flattenText($element): string
    {
        if (method_exists($element, 'getText')) {
            $text = $element->getText();
            return is_string($text) ? $text : '';
        }

        return '';
    }
}