<?php

namespace App\Helpers;

class ContentHelper
{
    public static function toMarkdown($text)
    {
        if (!$text) return null;

        // Normalize line breaks
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Split lines
        $lines = explode("\n", $text);

        $formatted = [];

        foreach ($lines as $line) {

            $line = trim($line);

            if ($line === '') {
                $formatted[] = '';
                continue;
            }

            // ✅ ONLY "What is..." should be heading
            if (preg_match('/^what is/i', $line)) {
                $formatted[] = "## " . $line;
                continue;
            }

            // ❌ REMOVE: definition as heading (fixed)

            // ✅ Bold labels
            if (preg_match('/^definition:/i', $line)) {
                $line = preg_replace('/^definition:\s*/i', "*Definition:*\n", $line);
            }

            if (preg_match('/^standard applied:/i', $line)) {
                $line = preg_replace('/^standard applied:\s*/i', "*Standard applied:*\n", $line);
            }

            // ✅ Replace arrow
            $line = str_replace('->', '→', $line);

            // ✅ Normalize bullets
            if (preg_match('/^-\s*/', $line)) {
                $line = '- ' . ltrim(substr($line, 1));
            }

            $formatted[] = $line;
        }

        // ✅ Add default heading if none exists
        if (!preg_grep('/^## /', $formatted)) {
            array_unshift($formatted, '## Content Overview', '');
        }

        return implode("\n", $formatted);
    }
}
