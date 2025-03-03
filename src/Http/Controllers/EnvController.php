<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class EnvController extends Controller
{
    /**
     * List of critical environment variables required for Laravel to function properly
     */
    private $criticalVariables = [
        'APP_KEY' => 'Required for encryption services, sessions, and cookies',
        'APP_ENV' => 'Defines the application environment (production, local, etc.)',
        'APP_DEBUG' => 'Controls error display and debugging information',
        'DB_CONNECTION' => 'Database connection type (mysql, pgsql, etc.)',
        'DB_HOST' => 'Database host address',
        'DB_PORT' => 'Database port',
        'DB_DATABASE' => 'Database name',
        'DB_USERNAME' => 'Database username',
        'DB_PASSWORD' => 'Database password',
        'CACHE_DRIVER' => 'Cache storage driver (file, redis, memcached, etc.)',
        'CACHE_STORE' => 'Cache store to use (defaults to CACHE_DRIVER if not specified)',
        'SESSION_DRIVER' => 'Session storage driver',
        'QUEUE_CONNECTION' => 'Queue connection type',
        'REDIS_HOST' => 'Redis host (if Redis is used for cache/session/queue)',
        'MAIL_MAILER' => 'Mail driver configuration',
        'FILESYSTEM_DISK' => 'Default filesystem disk',
    ];

    public function index()
    {
        $envVariables = $this->getEnvVariables();
        $groupedVariables = $this->groupVariables($envVariables);
        $duplicateKeys = $this->findDuplicateKeys();
        $missingCriticalVariables = $this->checkCriticalVariables($envVariables);
        $invalidCriticalVariables = $this->validateCriticalVariables($envVariables);
        
        return view('laravelops::env.index', [
            'allEnvVariables' => $envVariables,
            'envVariables' => $envVariables,
            'groupedVariables' => $groupedVariables,
            'duplicateKeys' => $duplicateKeys,
            'criticalVariables' => $this->criticalVariables,
            'missingCriticalVariables' => $missingCriticalVariables,
            'invalidCriticalVariables' => $invalidCriticalVariables
        ]);
    }
    
    public function clearCache()
    {
        try {
            Artisan::call('config:clear');
            return redirect()->route('laravelops.env.index')->with('success', 'Environment cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('laravelops.env.index')->with('error', 'Failed to clear environment cache: ' . $e->getMessage());
        }
    }
    
    private function getEnvVariables()
    {
        $envPath = base_path('.env');
        $variables = [];
        
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    $value = trim($parts[1]);
                    
                    // Remove quotes if present
                    if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                        $value = substr($value, 1, -1);
                    }
                    
                    $variables[$key] = $value;
                }
            }
        }
        
        return $variables;
    }
    
    private function findDuplicateKeys()
    {
        $envPath = base_path('.env');
        $keyOccurrences = [];
        $duplicateKeys = [];
        
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lineNumber = 0;
            
            foreach ($lines as $line) {
                $lineNumber++;
                
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                $parts = explode('=', $line, 2);
                if (count($parts) === 2) {
                    $key = trim($parts[0]);
                    
                    if (!isset($keyOccurrences[$key])) {
                        $keyOccurrences[$key] = [];
                    }
                    
                    $keyOccurrences[$key][] = [
                        'line' => $lineNumber,
                        'value' => trim($parts[1])
                    ];
                    
                    // If we've seen this key more than once, mark it as a duplicate
                    if (count($keyOccurrences[$key]) > 1) {
                        $duplicateKeys[$key] = $keyOccurrences[$key];
                    }
                }
            }
        }
        
        return $duplicateKeys;
    }
    
    /**
     * Check for missing critical environment variables
     */
    private function checkCriticalVariables(array $envVariables): array
    {
        $missing = [];
        
        // Check if using SQLite
        $usingSqlite = isset($envVariables['DB_CONNECTION']) && 
                      strtolower($envVariables['DB_CONNECTION']) === 'sqlite';
        
        // Define variables that are not required when using SQLite
        $sqliteExcludedVariables = [
            'DB_HOST',
            'DB_PORT',
            'DB_USERNAME',
            'DB_PASSWORD'
        ];
        
        foreach ($this->criticalVariables as $key => $description) {
            // Skip checking excluded variables when using SQLite
            if ($usingSqlite && in_array($key, $sqliteExcludedVariables)) {
                continue;
            }
            
            // Special handling for DB_DATABASE with SQLite
            if ($key === 'DB_DATABASE' && $usingSqlite) {
                if (!isset($envVariables[$key]) || $envVariables[$key] === '' || $envVariables[$key] === '""' || $envVariables[$key] === "''") {
                    $missing[$key] = 'For SQLite, DB_DATABASE should be set to ":memory:" or a valid file path.';
                }
                continue; // Skip the standard check below
            }
            
            // Standard check for missing or empty variables
            if (!isset($envVariables[$key]) || empty($envVariables[$key]) || $envVariables[$key] === '""' || $envVariables[$key] === "''") {
                $missing[$key] = $description;
            }
        }
        
        return $missing;
    }
    
    /**
     * Validate critical environment variables
     */
    private function validateCriticalVariables(array $envVariables): array
    {
        $invalid = [];
        
        // Check APP_KEY - should be a valid base64 string of appropriate length
        if (isset($envVariables['APP_KEY'])) {
            $appKey = $envVariables['APP_KEY'];
            // Remove 'base64:' prefix if present
            if (strpos($appKey, 'base64:') === 0) {
                $appKey = substr($appKey, 7);
            }
            
            // Check if it's a valid base64 string and has the correct length
            if (empty($appKey) || strlen(base64_decode($appKey, true)) !== 32) {
                $invalid['APP_KEY'] = 'Invalid APP_KEY format or length. Should be a 32-byte base64 encoded string.';
            }
        }
        
        // Check APP_ENV - should be one of the standard environments
        if (isset($envVariables['APP_ENV'])) {
            $validEnvs = ['local', 'development', 'testing', 'staging', 'production'];
            if (!in_array(strtolower($envVariables['APP_ENV']), $validEnvs)) {
                $invalid['APP_ENV'] = 'Unusual APP_ENV value. Common values are: local, development, testing, staging, production.';
            }
        }
        
        // Check APP_DEBUG - should be boolean
        if (isset($envVariables['APP_DEBUG'])) {
            $debug = strtolower($envVariables['APP_DEBUG']);
            if (!in_array($debug, ['true', 'false', '0', '1'])) {
                $invalid['APP_DEBUG'] = 'APP_DEBUG should be true, false, 0, or 1.';
            }
        }
        
        // Check DB_CONNECTION - should be a valid connection type
        if (isset($envVariables['DB_CONNECTION'])) {
            $validConnections = ['mysql', 'pgsql', 'sqlite', 'sqlsrv'];
            if (!in_array($envVariables['DB_CONNECTION'], $validConnections)) {
                $invalid['DB_CONNECTION'] = 'Invalid database connection. Should be one of: mysql, pgsql, sqlite, sqlsrv.';
            }
            
            // Connection-specific validations
            $dbConnection = strtolower($envVariables['DB_CONNECTION']);
            
            // SQLite specific checks
            if ($dbConnection === 'sqlite') {
                // For SQLite, check if DB_DATABASE is a valid path or :memory:
                if (isset($envVariables['DB_DATABASE'])) {
                    $dbPath = $envVariables['DB_DATABASE'];
                    if ($dbPath !== ':memory:') {
                        // If it's not :memory:, check if it's a valid path
                        // For relative paths, consider them relative to database_path()
                        if (strpos($dbPath, '/') !== 0) {
                            $dbPath = database_path($dbPath);
                        }
                        
                        // Check if the directory exists and is writable
                        $directory = dirname($dbPath);
                        if (!file_exists($directory) || !is_writable($directory)) {
                            $invalid['DB_DATABASE'] = 'SQLite database directory does not exist or is not writable.';
                        }
                    }
                }
            } 
            // MySQL, PostgreSQL, SQL Server checks
            else if (in_array($dbConnection, ['mysql', 'pgsql', 'sqlsrv'])) {
                // These connections require host, username, etc.
                $requiredVars = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'];
                foreach ($requiredVars as $var) {
                    if (!isset($envVariables[$var]) || empty($envVariables[$var])) {
                        $invalid[$var] = "Required for {$dbConnection} connection.";
                    }
                }
                
                // Check if port is numeric
                if (isset($envVariables['DB_PORT']) && !empty($envVariables['DB_PORT']) && !is_numeric($envVariables['DB_PORT'])) {
                    $invalid['DB_PORT'] = 'Database port must be a number.';
                }
            }
        }
        
        // Check cache configuration
        if (isset($envVariables['CACHE_DRIVER'])) {
            $validCacheDrivers = ['file', 'database', 'array', 'redis', 'memcached', 'dynamodb', 'octane', 'null'];
            if (!in_array(strtolower($envVariables['CACHE_DRIVER']), $validCacheDrivers)) {
                $invalid['CACHE_DRIVER'] = 'Invalid cache driver. Common values are: file, redis, memcached, array, null.';
            }
        }
        
        // If both CACHE_DRIVER and CACHE_STORE are set, they should be consistent
        if (isset($envVariables['CACHE_DRIVER']) && isset($envVariables['CACHE_STORE']) && 
            !empty($envVariables['CACHE_DRIVER']) && !empty($envVariables['CACHE_STORE']) &&
            $envVariables['CACHE_DRIVER'] !== $envVariables['CACHE_STORE']) {
            $invalid['CACHE_STORE'] = 'CACHE_STORE differs from CACHE_DRIVER. This may cause unexpected behavior unless intentional.';
        }
        
        return $invalid;
    }
    
    private function groupVariables(array $variables): array
    {
        $groups = [];
        
        foreach ($variables as $key => $value) {
            $prefix = strtok($key, '_');
            if (!isset($groups[$prefix])) {
                $groups[$prefix] = [];
            }
            $groups[$prefix][$key] = $value;
        }
        
        // Sort groups alphabetically
        ksort($groups);
        
        return $groups;
    }
}