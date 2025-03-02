<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Hamzasgd\LaravelOps\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LogViewerController extends Controller
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function index()
    {
        $files = $this->logService->getLogFiles();
        return view('laravelops::logs.index', compact('files'));
    }

    public function show(string $filename)
    {
        $logs = $this->logService->getLogContent($filename);
        return view('laravelops::logs.show', compact('logs', 'filename'));
    }

    public function live(Request $request)
    {
        $files = $this->logService->getLogFiles();
        $currentLog = $request->query('file', $files[0]['name'] ?? 'laravel.log');
        $logs = $this->logService->getLogContent($currentLog);
        
        return view('laravelops::logs.live', compact('logs', 'files', 'currentLog'));
    }
    
    /**
     * Format a stack trace to highlight application code
     *
     * @param string $stackTrace
     * @return string
     */
    public function formatStackTrace(string $stackTrace): string
    {
        $lines = explode("\n", $stackTrace);
        $formattedLines = [];
        
        $appPaths = [
            '/app/',
            '/config/',
            '/routes/',
            '/resources/',
            '/database/',
            '/tests/'
        ];
        
        foreach ($lines as $line) {
            // Check if the line is from application code
            $isAppCode = false;
            
            // Skip vendor, Laravel and framework code
            if (strpos($line, '/vendor/') !== false || 
                strpos($line, 'Illuminate\\') !== false || 
                strpos($line, 'Laravel\\') !== false) {
                $isAppCode = false;
            } 
            // Check if line contains an app path
            else {
                foreach ($appPaths as $path) {
                    if (strpos($line, $path) !== false) {
                        $isAppCode = true;
                        break;
                    }
                }
                
                // Also check for base application path
                if (!$isAppCode && strpos($line, base_path()) !== false) {
                    // If it has base path but none of the app directories,
                    // it might be root files like bootstrap/app.php, artisan, etc.
                    $isAppCode = true;
                }
            }
            
            if ($isAppCode) {
                $formattedLines[] = '<span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 px-1 py-0.5 rounded">' . htmlspecialchars($line) . '</span>';
            } else {
                $formattedLines[] = htmlspecialchars($line);
            }
        }
        
        return implode("\n", $formattedLines);
    }
} 