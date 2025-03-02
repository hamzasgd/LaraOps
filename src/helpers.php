<?php

if (!function_exists('formatStackTrace')) {
    /**
     * Format a stack trace to highlight application code
     *
     * @param string $stackTrace
     * @return string
     */
    function formatStackTrace(string $stackTrace): string
    {
        $lines = explode("\n", $stackTrace);
        $formattedLines = [];
        
        foreach ($lines as $line) {
            if (strpos($line, '/vendor/') === false && 
                (strpos($line, base_path()) !== false || preg_match('/#\d+\s+[^\/]*?\.php/', $line))) {
                $formattedLines[] = '<span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-1 py-0.5 rounded">' . htmlspecialchars($line) . '</span>';
            } else {
                $formattedLines[] = htmlspecialchars($line);
            }
        }
        
        return implode("\n", $formattedLines);
    }
} 