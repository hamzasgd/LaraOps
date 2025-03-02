<?php

namespace Hamzasgd\LaravelOps\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class LogService
{
    protected string $logPath;
    
    public function __construct()
    {
        $this->logPath = storage_path('logs');
    }

    public function getLogFiles(): array
    {
        $files = [];
        $finder = new Finder();
        
        if (!File::exists($this->logPath)) {
            return $files;
        }

        $finder->files()
            ->in($this->logPath)
            ->name('*.log')
            ->sortByModifiedTime();

        foreach ($finder as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatFileSize($file->getSize()),
                'modified' => $file->getMTime(),
                'path' => $file->getRealPath(),
            ];
        }

        return $files;
    }
    
    /**
     * Get the full path to a log file
     *
     * @param string $filename
     * @return string
     */
    protected function getLogPath(string $filename): string
    {
        return $this->logPath . DIRECTORY_SEPARATOR . $filename;
    }

    public function getLogContent($filename)
    {
        $path = $this->getLogPath($filename);
        if (!file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach ($matches as $match) {
            $datetime = $match[1];
            $environment = $match[2];
            $level = $match[3];
            $fullMessage = trim($match[4]);
            
            // Default values
            $message = '';
            $stackTrace = '';
            $exceptionData = [];
            
            // First, separate stack trace from the rest
            if (strpos($fullMessage, '[stacktrace]') !== false) {
                list($messagePart, $stackTracePart) = explode('[stacktrace]', $fullMessage, 2);
                $fullMessage = trim($messagePart);
                $stackTrace = '[stacktrace]' . $stackTracePart;
            }
            
            // Check if the message contains JSON data
            if (preg_match('/^(.*?)(\s+\{|\s+\[)/', $fullMessage, $msgParts)) {
                // This is a complex log entry with JSON data
                $message = trim($msgParts[1]);
                
                // Extract the JSON part - everything after the message
                $jsonPart = substr($fullMessage, strlen($message));
                
                // Try to extract exception data from the JSON part
                $this->extractExceptionData($jsonPart, $exceptionData);
            } else {
                // This is a simple log entry with just a message
                $message = $fullMessage;
            }
            
            $logs[] = [
                'datetime' => $datetime,
                'environment' => $environment,
                'level' => $level,
                'message' => $message,
                'stackTrace' => $stackTrace,
                'exceptionData' => $exceptionData,
            ];
        }

        return $logs;
    }

    /**
     * Extract exception data from a JSON string
     *
     * @param string $jsonPart
     * @param array &$exceptionData
     * @return void
     */
    protected function extractExceptionData($jsonPart, &$exceptionData)
    {
        // Try a direct approach first for common Laravel exception format
        if (preg_match('/\{"exception":"(.*?)(?:"\}|$)/', $jsonPart, $exMatch)) {
            $exceptionString = $exMatch[1];
            $exceptionData['exception'] = $exceptionString;
            
            // Try to extract structured data from the exception string
            if (preg_match('/\[object\] \((.*?)(?:\(code: (.*?)\))?: (.*?)(?: at (.*?):(\d+))?\)/', $exceptionString, $exMatches)) {
                $exceptionData['class'] = trim($exMatches[1]);
                $exceptionData['code'] = trim($exMatches[2] ?? '0');
                $exceptionData['message'] = trim($exMatches[3] ?? '');
                $exceptionData['file'] = trim($exMatches[4] ?? '');
                $exceptionData['line'] = trim($exMatches[5] ?? '');
            }
            return;
        }
        
        // If direct approach failed, try JSON parsing
        
        // Check if the JSON part is properly terminated
        if (substr_count($jsonPart, '{') > substr_count($jsonPart, '}')) {
            // Add missing closing braces
            $jsonPart .= str_repeat('}', substr_count($jsonPart, '{') - substr_count($jsonPart, '}'));
        }
        
        // Try to find a JSON object in the remaining text
        if (preg_match('/(\{.*\})/', $jsonPart, $jsonMatches)) {
            $jsonString = $jsonMatches[1];
            
            // Try to parse the JSON
            try {
                $jsonData = json_decode($jsonString, true);
                if ($jsonData && is_array($jsonData)) {
                    // Check if this is an exception or just regular data
                    if (isset($jsonData['exception'])) {
                        // This is an exception
                        $exceptionData = array_merge($exceptionData, $jsonData);
                        
                        // Process exception data
                        if (isset($exceptionData['exception'])) {
                            $exceptionString = $exceptionData['exception'];
                            
                            // Try to extract structured data from the exception string
                            if (preg_match('/\[object\] \((.*?)(?:\(code: (.*?)\))?: (.*?)(?: at (.*?):(\d+))?\)/', $exceptionString, $exMatches)) {
                                $exceptionData['class'] = trim($exMatches[1]);
                                $exceptionData['code'] = trim($exMatches[2] ?? '0');
                                $exceptionData['message'] = trim($exMatches[3] ?? '');
                                $exceptionData['file'] = trim($exMatches[4] ?? '');
                                $exceptionData['line'] = trim($exMatches[5] ?? '');
                            }
                        }
                    } else {
                        // This is just regular data
                        $exceptionData['data'] = $jsonData;
                        $exceptionData['is_data'] = true;
                    }
                }
            } catch (\Exception $e) {
                // JSON parsing failed, but we already have the message
            }
        }
    }

    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes > 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
} 