<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemInfoController extends ApiController
{
    /**
     * Get system information.
     *
     * @return JsonResponse
     */
    public function getInfo(): JsonResponse
    {
        try {
            $laravelVersion = app()->version();
            $phpVersion = PHP_VERSION;
            
            // Get database information
            $connection = config('database.default');
            $databaseConfig = config('database.connections.' . $connection);
            
            // Get database version based on connection type
            $databaseVersion = '';
            if ($connection === 'mysql') {
                $databaseVersion = DB::select('SELECT VERSION() as version')[0]->version ?? '';
            } elseif ($connection === 'pgsql') {
                $databaseVersion = DB::select('SHOW server_version')[0]->server_version ?? '';
            } elseif ($connection === 'sqlite') {
                $databaseVersion = 'SQLite ' . \SQLite3::version()['versionString'] ?? '';
            }
            
            // Get environment
            $environment = app()->environment();
            
            // Get cache driver
            $cacheDriver = config('cache.default');
            
            // Get session driver
            $sessionDriver = config('session.driver');
            
            // Get queue driver
            $queueDriver = config('queue.default');
            
            // Get mail driver
            $mailDriver = config('mail.default');
            
            // Get storage information
            $storageInfo = [
                'app' => $this->getDirectorySize(storage_path('app')),
                'logs' => $this->getDirectorySize(storage_path('logs')),
                'framework' => $this->getDirectorySize(storage_path('framework')),
            ];
            
            return $this->success([
                'php_version' => $phpVersion,
                'laravel_version' => $laravelVersion,
                'environment' => $environment,
                'database' => [
                    'connection' => $connection,
                    'version' => $databaseVersion,
                    'database' => $databaseConfig['database'] ?? '',
                    'host' => $databaseConfig['host'] ?? '',
                ],
                'cache_driver' => $cacheDriver,
                'session_driver' => $sessionDriver,
                'queue_driver' => $queueDriver,
                'mail_driver' => $mailDriver,
                'storage' => $storageInfo,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve system information: ' . $e->getMessage());
        }
    }
    
    /**
     * Get system resources usage.
     *
     * @return JsonResponse
     */
    public function getResources(): JsonResponse
    {
        try {
            // Get CPU usage (Linux only)
            $cpuUsage = 0;
            if (function_exists('sys_getloadavg') && PHP_OS !== 'WINNT') {
                $load = sys_getloadavg();
                $cpuUsage = $load[0]; // 1 minute load average
            }
            
            // Get memory usage
            $memoryTotal = 0;
            $memoryUsed = 0;
            $memoryFree = 0;
            
            if (PHP_OS !== 'WINNT') {
                // For Linux systems
                $memInfo = file_get_contents('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)/', $memInfo, $matches);
                $memoryTotal = isset($matches[1]) ? (int)$matches[1] * 1024 : 0; // Convert from KB to bytes
                
                preg_match('/MemFree:\s+(\d+)/', $memInfo, $matches);
                $memoryFree = isset($matches[1]) ? (int)$matches[1] * 1024 : 0; // Convert from KB to bytes
                
                $memoryUsed = $memoryTotal - $memoryFree;
            }
            
            // Get disk usage
            $diskTotal = disk_total_space(base_path());
            $diskFree = disk_free_space(base_path());
            $diskUsed = $diskTotal - $diskFree;
            
            return $this->success([
                'cpu' => [
                    'usage' => $cpuUsage,
                    'cores' => $this->getCpuCores(),
                ],
                'memory' => [
                    'total' => $memoryTotal,
                    'used' => $memoryUsed,
                    'free' => $memoryFree,
                    'usage_percent' => $memoryTotal > 0 ? round(($memoryUsed / $memoryTotal) * 100, 2) : 0,
                ],
                'disk' => [
                    'total' => $diskTotal,
                    'used' => $diskUsed,
                    'free' => $diskFree,
                    'usage_percent' => $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 2) : 0,
                ],
                'timestamp' => now()->timestamp,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve system resources: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear application cache.
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            
            return $this->success([], 'Application cache cleared successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to clear cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear compiled views.
     *
     * @return JsonResponse
     */
    public function clearViews(): JsonResponse
    {
        try {
            Artisan::call('view:clear');
            
            return $this->success([], 'Compiled views cleared successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to clear views: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear route cache.
     *
     * @return JsonResponse
     */
    public function clearRoutes(): JsonResponse
    {
        try {
            Artisan::call('route:clear');
            
            return $this->success([], 'Route cache cleared successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to clear routes: ' . $e->getMessage());
        }
    }
    
    /**
     * Create storage link.
     *
     * @return JsonResponse
     */
    public function createStorageLink(): JsonResponse
    {
        try {
            Artisan::call('storage:link');
            
            return $this->success([], 'Storage link created successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to create storage link: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the size of a directory.
     *
     * @param string $path
     * @return int
     */
    private function getDirectorySize(string $path): int
    {
        $size = 0;
        
        if (!is_dir($path)) {
            return $size;
        }
        
        $files = scandir($path);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $filePath = $path . '/' . $file;
            
            if (is_dir($filePath)) {
                $size += $this->getDirectorySize($filePath);
            } else {
                $size += filesize($filePath);
            }
        }
        
        return $size;
    }
    
    /**
     * Get the number of CPU cores.
     *
     * @return int
     */
    private function getCpuCores(): int
    {
        $cores = 1; // Default to 1 core
        
        if (PHP_OS === 'WINNT') {
            // Windows
            $process = @popen('wmic cpu get NumberOfCores', 'rb');
            if ($process) {
                fgets($process); // Skip the first line (header)
                $cores = (int) fgets($process);
                pclose($process);
            }
        } else {
            // Linux/Unix
            $process = @popen('nproc', 'rb');
            if ($process) {
                $cores = (int) fgets($process);
                pclose($process);
            }
            
            if ($cores <= 1) {
                // Try another method
                $cpuInfo = file_get_contents('/proc/cpuinfo');
                preg_match_all('/^processor/m', $cpuInfo, $matches);
                $cores = count($matches[0]);
            }
        }
        
        return max(1, $cores); // Ensure at least 1 core
    }
} 