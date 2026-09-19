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
use Smalot\PdfParser\Parser as PdfParser;

class FileToHtmlController extends Controller
{
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

            case 'html':
            case 'htm':
                return file_get_contents($path);

            case 'txt':
            case 'md':
            case 'csv':
            case 'json':
                return '<pre>' . htmlspecialchars(file_get_contents($path)) . '</pre>';

            case 'pdf':
                $parser = new PdfParser();
                $text = $parser->parseFile($path)->getText();
                return '<p>' . nl2br(htmlspecialchars($text)) . '</p>';

            default:
                throw new \RuntimeException('Unsupported file type: ' . $extension);
        }
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
                // Collect this run of consecutive list items (same list block)
                // into one <ul>/<ol>, tracking depth for nested lists.
                $listItems = [];
                while ($i < $count && ($elements[$i] instanceof ListItem || $elements[$i] instanceof ListItemRun)) {
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
     * @param array<ListItem|ListItemRun> $listItems
     */
    private function renderListBlock(array $listItems): string
    {
        // Build a flat sequence honoring depth, opening/closing nested
        // <ul>/<ol> as depth changes. Word list depth is 0-indexed.
        $html = '';
        $currentDepth = -1;
        $tagStack = []; // stack of 'ul'|'ol' per open depth level

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

        // Close any remaining open lists
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
            $text = htmlspecialchars($this->flattenText($element));
            return "<h{$depth}>{$text}</h{$depth}>";
        }

        if ($element instanceof Table) {
            return $this->renderTable($element);
        }

        if ($element instanceof TextRun) {
            // Word docs sometimes have "Heading 1/2/3" styled paragraphs that
            // PhpWord's reader doesn't map to a Title element (custom/renamed
            // style, or the style ID doesn't match PhpWord's expected pattern).
            // Detect that case directly from the paragraph style name.
            $headingLevel = $this->detectHeadingLevel($element);

            if ($headingLevel !== null) {
                $text = $this->extractRunText($element);
                return "<h{$headingLevel}>{$text}</h{$headingLevel}>";
            }

            return '<p>' . $this->extractRunText($element) . '</p>';
        }

        if ($element instanceof Text) {
            return '<p>' . htmlspecialchars($element->getText()) . '</p>';
        }

        if ($element instanceof AbstractContainer) {
            return $this->renderElements($element->getElements());
        }

        return '';
    }

    /**
     * Returns a heading level (1-6) if this TextRun paragraph should be
     * rendered as a heading, or null if it's a normal paragraph.
     *
     * Checks two things:
     *   1. The paragraph's style name/ID (e.g. "Heading1", "Heading 2",
     *      a custom style whose name contains "heading").
     *   2. A fallback heuristic: a short, fully-bold, standalone line with
     *      no trailing sentence punctuation — common when headings were
     *      typed with manual bold/size formatting rather than a real
     *      Word Heading style.
     */
    private function detectHeadingLevel(TextRun $run): ?int
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

        // Fallback heuristic for manually-formatted "headings"
        $text = trim(strip_tags($this->extractRunText($run)));
        if ($text === '') {
            return null;
        }

        $allBold = true;
        $maxFontSize = 0;
        $hasContent = false;

        foreach ($run->getElements() as $child) {
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

        // Short (<=100 chars), fully bold, no sentence-ending punctuation,
        // and either a numbered-section pattern ("1. Introduction") or a
        // noticeably larger font size — treat as a heading.
        $looksLikeHeading = $hasContent
            && $allBold
            && mb_strlen($text) <= 100
            && !preg_match('/[.!?,;:]$/', $text);

        if ($looksLikeHeading) {
            if (preg_match('/^\d+(\.\d+)*\.?\s+/', $text)) {
                return 2; // "1. Introduction", "2.1 Purpose" → H2
            }
            if ($maxFontSize >= 16) {
                return 1; // large bold standalone text → H1
            }
            return 2; // default bold-heading fallback → H2
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

    /**
     * Extracts text from a TextRun/ListItemRun's child Text elements,
     * preserving bold/italic/underline inline formatting.
     */
    private function extractRunText($run): string
    {
        if (!method_exists($run, 'getElements')) {
            return method_exists($run, 'getText') ? htmlspecialchars((string) $run->getText()) : '';
        }

        $html = '';

        foreach ($run->getElements() as $child) {
            if ($child instanceof Text) {
                $text = htmlspecialchars($child->getText());
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
            } elseif (method_exists($child, 'getText')) {
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
