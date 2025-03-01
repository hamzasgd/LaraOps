<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class EnvController extends Controller
{
    public function index()
    {
        // Get all environment variables
        $envVariables = $this->getEnvironmentVariables();
        
        // Group variables by common prefixes
        $groupedVariables = $this->groupVariables($envVariables);
        
        return view('laravelops::env.index', [
            'groupedVariables' => $groupedVariables,
            'allVariables' => $envVariables
        ]);
    }
    
    public function clearCache()
    {
        try {
            // Clear config cache
            Artisan::call('config:clear');
            
            // Clear application cache
            Artisan::call('cache:clear');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Configuration cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }
    
    private function getEnvironmentVariables()
    {
        $variables = [];
        $duplicates = [];
        
        // Get all environment variables
        $envVars = $_ENV;
        
        // Filter out sensitive variables if needed
        $sensitiveKeys = ['DB_PASSWORD', 'REDIS_PASSWORD', 'MAIL_PASSWORD', 'AWS_SECRET'];
        
        // First pass to identify duplicates in the raw env file
        $envFilePath = app()->environmentFilePath();
        if (file_exists($envFilePath)) {
            $envFileContents = file_get_contents($envFilePath);
            $lines = explode("\n", $envFileContents);
            $keyCount = [];
            
            foreach ($lines as $line) {
                // Skip comments and empty lines
                if (empty($line) || strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Extract key from line (KEY=value format)
                if (strpos($line, '=') !== false) {
                    $parts = explode('=', $line, 2);
                    $key = trim($parts[0]);
                    
                    if (!empty($key)) {
                        $keyCount[$key] = isset($keyCount[$key]) ? $keyCount[$key] + 1 : 1;
                        
                        if ($keyCount[$key] > 1) {
                            $duplicates[$key] = true;
                        }
                    }
                }
            }
        }
        
        // Process environment variables
        foreach ($envVars as $key => $value) {
            // Skip non-string values and internal PHP variables
            if (!is_string($value) || strpos($key, 'PHP_') === 0) {
                continue;
            }
            
            // Mask sensitive values
            if (in_array($key, $sensitiveKeys) && !empty($value)) {
                $value = '********';
            }
            
            $variables[$key] = [
                'value' => $value,
                'isDuplicate' => isset($duplicates[$key])
            ];
        }
        
        // Sort alphabetically
        ksort($variables);
        
        return $variables;
    }
    
    private function groupVariables(array $variables)
    {
        $groups = [];
        $ungrouped = [];
        
        // Common prefixes to group by
        $commonPrefixes = [
            'APP_', 'DB_', 'MAIL_', 'QUEUE_', 'CACHE_', 
            'SESSION_', 'REDIS_', 'AWS_', 'LOG_', 'BROADCAST_'
        ];
        
        foreach ($variables as $key => $value) {
            $grouped = false;
            
            foreach ($commonPrefixes as $prefix) {
                if (strpos($key, $prefix) === 0) {
                    $groupName = rtrim($prefix, '_');
                    $groups[$groupName][$key] = $value;
                    $grouped = true;
                    break;
                }
            }
            
            if (!$grouped) {
                $ungrouped[$key] = $value;
            }
        }
        
        // Add ungrouped variables as "Other"
        if (!empty($ungrouped)) {
            $groups['Other'] = $ungrouped;
        }
        
        // Sort groups by name
        ksort($groups);
        
        return $groups;
    }
} 