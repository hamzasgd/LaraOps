<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LogViewerController extends ApiController
{
    /**
     * Get all log files.
     *
     * @return JsonResponse
     */
    public function getLogs(): JsonResponse
    {
        try {
            $logPath = storage_path('logs');
            $logFiles = [];
            
            if (File::exists($logPath)) {
                $files = File::files($logPath);
                
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                        $logFiles[] = [
                            'name' => $file->getFilename(),
                            'size' => $file->getSize(),
                            'modified_at' => date('Y-m-d H:i:s', $file->getMTime()),
                        ];
                    }
                }
                
                // Sort by modified date (newest first)
                usort($logFiles, function ($a, $b) {
                    return strtotime($b['modified_at']) - strtotime($a['modified_at']);
                });
            }
            
            return response()->json($logFiles);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve log files: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the content of a log file.
     *
     * @param string $filename
     * @return JsonResponse
     */
    public function getLogContent(string $filename): JsonResponse
    {
        try {
            $logPath = storage_path('logs/' . $filename);
            
            if (!File::exists($logPath)) {
                return $this->error('Log file not found', 404);
            }
            
            $content = File::get($logPath);
            $logs = $this->parseLogContent($content);
            
            return response()->json($logs);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve log content: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a log file.
     *
     * @param string $filename
     * @return JsonResponse
     */
    public function deleteLog(string $filename): JsonResponse
    {
        try {
            $logPath = storage_path('logs/' . $filename);
            
            if (!File::exists($logPath)) {
                return $this->error('Log file not found', 404);
            }
            
            File::delete($logPath);
            
            return $this->success([], 'Log file deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete log file: ' . $e->getMessage());
        }
    }
    
    /**
     * Parse the log file content into structured data.
     *
     * @param string $content
     * @return array
     */
    private function parseLogContent(string $content): array
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}\.?(\d*)?(\+\d{2}:\d{2})?)\]\s+(\w+)\.(\w+):(.*?)(?=\[|$)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        $logs = [];
        
        foreach ($matches as $match) {
            $timestamp = $match[1];
            $level = $match[4];
            $environment = $match[5];
            $message = trim($match[6]);
            
            // Extract context and stack trace if available
            $contextPattern = '/\{.*\}/s';
            $stackPattern = '/Stack trace:(.*?)$/s';
            
            $context = '';
            $stack = '';
            
            if (preg_match($contextPattern, $message, $contextMatch)) {
                $context = $contextMatch[0];
                $message = trim(str_replace($context, '', $message));
            }
            
            if (preg_match($stackPattern, $message, $stackMatch)) {
                $stack = trim($stackMatch[1]);
                $message = trim(str_replace($stackMatch[0], '', $message));
            }
            
            $logs[] = [
                'timestamp' => $timestamp,
                'level' => strtolower($level),
                'environment' => $environment,
                'message' => $message,
                'context' => $context,
                'stack' => $stack,
            ];
        }
        
        // Sort by timestamp (newest first)
        usort($logs, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return $logs;
    }
} 