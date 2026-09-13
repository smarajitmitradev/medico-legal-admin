<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GroqAiService;
use Illuminate\Http\Request;

class FileToHtmlController extends Controller
{
    protected $groq;

    public function __construct(GroqAiService $groq)
    {
        $this->groq = $groq;
    }

    public function convert(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $rawContent = $this->extractText($file, $extension);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not read file: ' . $e->getMessage()], 422);
        }

        if (trim($rawContent) === '') {
            return response()->json(['error' => 'File appears to be empty or unsupported.'], 422);
        }

        // Groq has token limits — trim very large files
        $rawContent = mb_substr($rawContent, 0, 10000);

        $systemPrompt = 'You convert raw document text into clean semantic HTML '
            . '(using tags like <h1>, <h2>, <p>, <ul>, <li>, <strong>, <table> as appropriate). '
            . 'Return ONLY the HTML markup as a SINGLE LINE with absolutely no line breaks '
            . 'and no unnecessary whitespace between tags. Do not wrap it in markdown code '
            . 'fences, do not add any explanation or commentary — output raw HTML only.';

        $html = $this->groq->chat($rawContent, $systemPrompt, 7000);

        if ($html === 'Groq API key not configured') {
            return response()->json(['error' => $html], 500);
        }

        if (str_starts_with($html, '{"error"') || str_contains($html, '"errors"')) {
            // service returned raw error body from a failed request
            return response()->json(['error' => 'Groq API request failed: ' . $html], 500);
        }

        // Hard-enforce single line / no extra whitespace, just in case the model doesn't comply perfectly
        $html = preg_replace('/\r\n|\r|\n/', '', $html);
        $html = preg_replace('/>\s+</', '><', trim($html));

        return response()->json(['html' => $html]);
    }

    private function extractText($file, string $extension): string
    {
        $path = $file->getRealPath();

        switch ($extension) {
            case 'txt':
            case 'md':
            case 'csv':
            case 'json':
            case 'html':
            case 'htm':
                return file_get_contents($path);

            case 'pdf':
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                return $pdf->getText();

            case 'docx':
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        } elseif (method_exists($element, 'getElements')) {
                            foreach ($element->getElements() as $childElement) {
                                if (method_exists($childElement, 'getText')) {
                                    $text .= $childElement->getText() . "\n";
                                }
                            }
                        }
                    }
                }
                return $text;

            default:
                return file_get_contents($path); // best-effort fallback
        }
    }
}