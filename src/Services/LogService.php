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

    public function getLogContent(string $filename): array
    {
        $path = $this->logPath . DIRECTORY_SEPARATOR . $filename;
        
        if (!File::exists($path)) {
            return [];
        }

        $content = File::get($path);
        return $this->parseLogContent($content);
    }

    protected function parseLogContent(string $content): array
    {
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach ($matches as $match) {
            $logs[] = [
                'datetime' => $match[1],
                'environment' => $match[2],
                'level' => $match[3],
                'message' => trim($match[4]),
            ];
        }

        return array_reverse($logs);
    }

    protected function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes > 1024) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
} 