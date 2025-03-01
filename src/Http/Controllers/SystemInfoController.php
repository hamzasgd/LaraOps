<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SystemInfoController extends Controller
{
    public function index()
    {
        // Collect all health checks
        $healthChecks = $this->performHealthChecks();
        
        // Count critical issues
        $criticalIssues = collect($healthChecks)->where('status', 'critical')->count();
        
        return view('laravelops::system.index', [
            'systemInfo' => $this->getSystemInfo(),
            'laravelInfo' => $this->getLaravelInfo(),
            'databaseInfo' => $this->getDatabaseInfo(),
            'storageInfo' => $this->getStorageInfo(),
            'webServerInfo' => $this->getWebServerInfo(),
            'healthChecks' => $healthChecks,
            'criticalIssues' => $criticalIssues
        ]);
    }
    
    private function getSystemInfo()
    {
        return [
            'PHP Version' => phpversion(),
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'Server OS' => php_uname('s') . ' ' . php_uname('r'),
            'Server IP' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'Max Execution Time' => ini_get('max_execution_time') . ' seconds',
            'Max Input Time' => ini_get('max_input_time') . ' seconds',
            'Memory Limit' => ini_get('memory_limit'),
            'Upload Max Filesize' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'Default Socket Timeout' => ini_get('default_socket_timeout') . ' seconds',
            'Request Timeout' => ini_get('request_terminate_timeout') ?? 'Not set',
            'Output Buffering' => ini_get('output_buffering') ? 'Enabled (' . ini_get('output_buffering') . ')' : 'Disabled',
            'Max Input Vars' => ini_get('max_input_vars'),
            'Display Errors' => ini_get('display_errors') ? 'Enabled' : 'Disabled',
            'Error Reporting' => $this->getErrorReportingLevel(),
            'Opcache Enabled' => ini_get('opcache.enable') ? 'Yes' : 'No',
            'Loaded Extensions' => $this->formatExtensions(),
        ];
    }
    
    private function getErrorReportingLevel()
    {
        $level = error_reporting();
        $constants = [
            'E_ALL' => E_ALL,
            'E_ERROR' => E_ERROR,
            'E_WARNING' => E_WARNING,
            'E_PARSE' => E_PARSE,
            'E_NOTICE' => E_NOTICE,
            'E_STRICT' => E_STRICT,
            'E_DEPRECATED' => E_DEPRECATED
        ];
        
        $result = [];
        foreach ($constants as $name => $value) {
            if (($level & $value) == $value) {
                $result[] = $name;
            }
        }
        
        return empty($result) ? 'None' : implode(', ', $result);
    }
    
    private function formatExtensions()
    {
        $extensions = get_loaded_extensions();
        sort($extensions);
        
        // Take only the first few to avoid overwhelming the UI
        $extensionsList = array_slice($extensions, 0, 5);
        $remaining = count($extensions) - count($extensionsList);
        
        if ($remaining > 0) {
            $extensionsList[] = "... and {$remaining} more";
        }
        
        return implode(', ', $extensionsList);
    }
    
    private function getLaravelInfo()
    {
        return [
            'Laravel Version' => app()->version(),
            'Environment' => app()->environment(),
            'Debug Mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'App URL' => config('app.url'),
            'Cache Driver' => config('cache.default'),
            'Session Driver' => config('session.driver'),
            'Session Lifetime' => config('session.lifetime') . ' minutes',
            'Queue Driver' => config('queue.default'),
            'Mail Driver' => config('mail.default'),
            'Timezone' => config('app.timezone'),
            'Locale' => config('app.locale'),
            'Fallback Locale' => config('app.fallback_locale'),
            'Encryption Key Set' => !empty(config('app.key')) ? 'Yes' : 'No',
            'Maintenance Mode' => app()->isDownForMaintenance() ? 'Enabled' : 'Disabled',
            'Auto-loaded Service Providers' => count(app()->getLoadedProviders()),
            'Composer Version' => $this->getComposerVersion(),
        ];
    }
    
    private function getComposerVersion()
    {
        try {
            $process = new \Symfony\Component\Process\Process(['composer', '--version']);
            $process->run();
            return $process->isSuccessful() ? trim($process->getOutput()) : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }
    
    private function getDatabaseInfo()
    {
        try {
            $connection = DB::connection()->getPdo();
            $driver = DB::connection()->getDriverName();
            $version = match($driver) {
                'mysql' => DB::select('SELECT version() as version')[0]->version,
                'pgsql' => DB::select('SELECT version() as version')[0]->version,
                'sqlite' => 'SQLite ' . DB::connection()->getPdo()->getAttribute(\PDO::ATTR_CLIENT_VERSION),
                'sqlsrv' => DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION),
                default => 'Unknown'
            };
            
            return [
                'Connection' => 'Connected',
                'Driver' => $driver,
                'Database Name' => DB::connection()->getDatabaseName(),
                'Version' => $version,
                'Charset' => DB::connection()->getConfig('charset'),
                'Collation' => DB::connection()->getConfig('collation'),
                'Prefix' => DB::connection()->getConfig('prefix'),
            ];
        } catch (\Exception $e) {
            return [
                'Connection' => 'Failed',
                'Error' => $e->getMessage(),
            ];
        }
    }
    
    private function getStorageInfo()
    {
        $publicPath = public_path('storage');
        $storageLinkExists = file_exists($publicPath) && is_link($publicPath);
        
        $storageInfo = [
            'Storage Link' => $storageLinkExists ? 'Created' : 'Not Created',
        ];
        
        // Check directory permissions
        $directories = ['storage/app', 'storage/framework', 'storage/logs', 'bootstrap/cache'];
        foreach ($directories as $directory) {
            $path = base_path($directory);
            $isWritable = is_writable($path);
            $perms = $this->getPermissions($path);
            $owner = $this->getOwner($path);
            
            $storageInfo[$directory . ' Directory'] = $isWritable 
                ? "Writable (Permissions: {$perms}, Owner: {$owner})" 
                : "Not Writable (Permissions: {$perms}, Owner: {$owner})";
        }
        
        // Get disk usage
        $storageInfo['Total Disk Space'] = $this->formatBytes(disk_total_space(storage_path()));
        $storageInfo['Free Disk Space'] = $this->formatBytes(disk_free_space(storage_path()));
        
        // Get log file size
        $logSize = 0;
        $logFiles = File::files(storage_path('logs'));
        foreach ($logFiles as $file) {
            $logSize += $file->getSize();
        }
        $storageInfo['Log Files Size'] = $this->formatBytes($logSize);
        
        return $storageInfo;
    }
    
    /**
     * Get the permissions of a file or directory in human-readable format (e.g., 0755)
     */
    private function getPermissions($path)
    {
        if (!file_exists($path)) {
            return 'N/A';
        }
        
        $perms = fileperms($path);
        
        // Format the permissions as a 4-digit octal number
        return substr(sprintf('%o', $perms), -4);
    }
    
    /**
     * Get the owner and group of a file or directory
     */
    private function getOwner($path)
    {
        if (!file_exists($path)) {
            return 'N/A';
        }
        
        $owner = 'unknown';
        $group = 'unknown';
        
        // Try to get owner name
        if (function_exists('posix_getpwuid')) {
            $ownerInfo = posix_getpwuid(fileowner($path));
            $owner = $ownerInfo['name'] ?? fileowner($path);
        } else {
            $owner = fileowner($path);
        }
        
        // Try to get group name
        if (function_exists('posix_getgrgid')) {
            $groupInfo = posix_getgrgid(filegroup($path));
            $group = $groupInfo['name'] ?? filegroup($path);
        } else {
            $group = filegroup($path);
        }
        
        return "{$owner}:{$group}";
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            
            return redirect()->route('laravelops.system.index')
                ->with('success', 'Cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('laravelops.system.index')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function clearViews()
    {
        try {
            Artisan::call('view:clear');
            
            return redirect()->route('laravelops.system.index')
                ->with('success', 'View cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('laravelops.system.index')
                ->with('error', 'Failed to clear view cache: ' . $e->getMessage());
        }
    }

    public function clearRoutes()
    {
        try {
            Artisan::call('route:clear');
            
            return redirect()->route('laravelops.system.index')
                ->with('success', 'Route cache cleared successfully!');
        } catch (\Exception $e) {
            return redirect()->route('laravelops.system.index')
                ->with('error', 'Failed to clear route cache: ' . $e->getMessage());
        }
    }

    public function createStorageLink()
    {
        try {
            Artisan::call('storage:link');
            
            return redirect()->route('laravelops.system.index')
                ->with('success', 'Storage link created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('laravelops.system.index')
                ->with('error', 'Failed to create storage link: ' . $e->getMessage());
        }
    }

    private function getWebServerInfo()
    {
        $serverInfo = [];
        
        // Basic server information
        $serverInfo['Server Software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
        $serverInfo['Server Protocol'] = $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown';
        $serverInfo['Server Port'] = $_SERVER['SERVER_PORT'] ?? 'Unknown';
        $serverInfo['Document Root'] = $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown';
        
        // Try to detect server type and get specific info
        if (strpos(strtolower($_SERVER['SERVER_SOFTWARE'] ?? ''), 'nginx') !== false) {
            $serverInfo['Server Type'] = 'Nginx';
            // Add Nginx-specific info if available
        } elseif (strpos(strtolower($_SERVER['SERVER_SOFTWARE'] ?? ''), 'apache') !== false) {
            $serverInfo['Server Type'] = 'Apache';
            $serverInfo['Apache Modules'] = $this->getApacheModules();
        } else {
            $serverInfo['Server Type'] = 'Unknown';
        }
        
        // Add PHP-FPM info if available
        if (function_exists('fpm_get_status')) {
            $fpmStatus = @fpm_get_status();
            if ($fpmStatus) {
                $serverInfo['PHP-FPM'] = 'Enabled';
                $serverInfo['PHP-FPM Listen Queue'] = $fpmStatus['listen queue'] ?? 'Unknown';
                $serverInfo['PHP-FPM Max Children'] = $fpmStatus['max children reached'] ?? 'Unknown';
            }
        }
        
        // Add timeout information
        $serverInfo['PHP Max Execution Time'] = ini_get('max_execution_time') . ' seconds';
        $serverInfo['PHP Max Input Time'] = ini_get('max_input_time') . ' seconds';
        $serverInfo['Default Socket Timeout'] = ini_get('default_socket_timeout') . ' seconds';
        
        // Try to get web server timeout (this is server-specific)
        if (function_exists('apache_get_modules')) {
            $serverInfo['Apache Timeout'] = $this->getApacheTimeout();
        }
        
        return $serverInfo;
    }

    private function getApacheModules()
    {
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $modulesList = array_slice($modules, 0, 5);
            $remaining = count($modules) - count($modulesList);
            
            if ($remaining > 0) {
                $modulesList[] = "... and {$remaining} more";
            }
            
            return implode(', ', $modulesList);
        }
        
        return 'Unknown (apache_get_modules function not available)';
    }

    private function getApacheTimeout()
    {
        // Try to get Apache timeout from server configuration
        // This is a simplified approach and might not work in all environments
        try {
            $process = new \Symfony\Component\Process\Process(['apache2ctl', '-t', '-D', 'DUMP_INCLUDES']);
            $process->run();
            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                if (preg_match('/Timeout\s+(\d+)/', $output, $matches)) {
                    return $matches[1] . ' seconds';
                }
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        return 'Unknown';
    }

    /**
     * Perform various health checks on the application
     */
    private function performHealthChecks()
    {
        $checks = [];
        
        // Check database connection
        try {
            DB::connection()->getPdo();
            $checks['database'] = [
                'name' => 'Database Connection',
                'status' => 'ok',
                'message' => 'Connected successfully'
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'name' => 'Database Connection',
                'status' => 'critical',
                'message' => 'Failed: ' . $e->getMessage()
            ];
        }
        
        // Check directory permissions
        $directories = ['storage/app', 'storage/framework', 'storage/logs', 'bootstrap/cache'];
        foreach ($directories as $directory) {
            $path = base_path($directory);
            $isWritable = is_writable($path);
            $perms = $this->getPermissions($path);
            
            $checks[$directory] = [
                'name' => $directory . ' Directory',
                'status' => $isWritable ? 'ok' : 'critical',
                'message' => $isWritable 
                    ? "Writable (Permissions: {$perms})" 
                    : "Not writable (Permissions: {$perms})"
            ];
        }
        
        // Check storage link
        $publicPath = public_path('storage');
        $storageLinkExists = file_exists($publicPath) && is_link($publicPath);
        $checks['storage_link'] = [
            'name' => 'Storage Link',
            'status' => $storageLinkExists ? 'ok' : 'warning',
            'message' => $storageLinkExists ? 'Created' : 'Not created'
        ];
        
        // Check environment file
        $envExists = file_exists(base_path('.env'));
        $checks['env_file'] = [
            'name' => 'Environment File',
            'status' => $envExists ? 'ok' : 'critical',
            'message' => $envExists ? 'Exists' : 'Missing'
        ];
        
        // Check app key
        $appKey = config('app.key');
        $checks['app_key'] = [
            'name' => 'Application Key',
            'status' => !empty($appKey) ? 'ok' : 'critical',
            'message' => !empty($appKey) ? 'Set' : 'Not set'
        ];
        
        // Check debug mode in production
        $isProduction = app()->environment('production');
        $debugEnabled = config('app.debug');
        $checks['debug_mode'] = [
            'name' => 'Debug Mode',
            'status' => (!$isProduction || !$debugEnabled) ? 'ok' : 'warning',
            'message' => $debugEnabled 
                ? ($isProduction ? 'Enabled in production (security risk)' : 'Enabled (development mode)') 
                : 'Disabled'
        ];
        
        // Check disk space
        $freeDiskSpace = disk_free_space(storage_path());
        $totalDiskSpace = disk_total_space(storage_path());
        $diskSpacePercentage = ($freeDiskSpace / $totalDiskSpace) * 100;
        
        $diskStatus = 'ok';
        if ($diskSpacePercentage < 10) {
            $diskStatus = 'critical';
        } elseif ($diskSpacePercentage < 20) {
            $diskStatus = 'warning';
        }
        
        $checks['disk_space'] = [
            'name' => 'Disk Space',
            'status' => $diskStatus,
            'message' => round($diskSpacePercentage, 2) . '% free (' . 
                $this->formatBytes($freeDiskSpace) . ' of ' . 
                $this->formatBytes($totalDiskSpace) . ')'
        ];
        
        return $checks;
    }

    public function getSystemResources()
    {
        try {
            $data = [
                'success' => true,
                'memory' => $this->getMemoryUsage(),
                'cpu' => $this->getCpuUsage(),
                'time' => now()->format('H:i:s')
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function getMemoryUsage()
    {
        if (function_exists('shell_exec')) {
            if (PHP_OS_FAMILY === 'Linux') {
                $free = shell_exec('free -m');
                if ($free !== null) {
                    $lines = explode("\n", $free);
                    $memory = explode(" ", preg_replace('/\s+/', ' ', $lines[1]));
                    $total = (int)$memory[1];
                    $used = (int)$memory[2];
                    $percentage = round(($used / $total) * 100, 1);
                    
                    return [
                        'total' => $total,
                        'used' => $used,
                        'free' => $total - $used,
                        'percentage' => $percentage,
                        'formatted' => $this->formatBytes($used * 1024 * 1024) . ' / ' . $this->formatBytes($total * 1024 * 1024)
                    ];
                }
            } elseif (PHP_OS_FAMILY === 'Windows') {
                $cmd = 'wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value';
                $result = shell_exec($cmd);
                if ($result !== null) {
                    preg_match('/TotalVisibleMemorySize=(\d+)/i', $result, $matches);
                    $total = isset($matches[1]) ? (int)$matches[1] : 0;
                    
                    preg_match('/FreePhysicalMemory=(\d+)/i', $result, $matches);
                    $free = isset($matches[1]) ? (int)$matches[1] : 0;
                    
                    $used = $total - $free;
                    $percentage = round(($used / $total) * 100, 1);
                    
                    return [
                        'total' => round($total / 1024),
                        'used' => round($used / 1024),
                        'free' => round($free / 1024),
                        'percentage' => $percentage,
                        'formatted' => $this->formatBytes($used * 1024) . ' / ' . $this->formatBytes($total * 1024)
                    ];
                }
            }
        }
        
        // Fallback to PHP memory info
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        return [
            'php_limit' => $memoryLimit,
            'php_usage' => $this->formatBytes($memoryUsage),
            'php_peak' => $this->formatBytes($memoryPeak),
            'percentage' => null,
            'formatted' => $this->formatBytes($memoryUsage) . ' (Peak: ' . $this->formatBytes($memoryPeak) . ')'
        ];
    }

    private function getCpuUsage()
    {
        if (function_exists('shell_exec')) {
            if (PHP_OS_FAMILY === 'Linux') {
                $load = sys_getloadavg();
                $cpuCores = (int)shell_exec('nproc');
                
                if ($cpuCores > 0) {
                    $percentage = round(($load[0] / $cpuCores) * 100, 1);
                    
                    return [
                        'load' => $load,
                        'cores' => $cpuCores,
                        'percentage' => min(100, $percentage),
                        'formatted' => "Load: {$load[0]} / {$cpuCores} cores ({$percentage}%)"
                    ];
                }
            } elseif (PHP_OS_FAMILY === 'Windows') {
                $cmd = 'wmic cpu get LoadPercentage /Value';
                $result = shell_exec($cmd);
                if ($result !== null) {
                    preg_match('/LoadPercentage=(\d+)/i', $result, $matches);
                    $percentage = isset($matches[1]) ? (int)$matches[1] : 0;
                    
                    return [
                        'percentage' => $percentage,
                        'formatted' => "CPU Load: {$percentage}%"
                    ];
                }
            }
        }
        
        return [
            'percentage' => null,
            'formatted' => 'CPU usage information not available'
        ];
    }
} 