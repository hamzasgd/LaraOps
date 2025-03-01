<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class EnvironmentController extends ApiController
{
    /**
     * Get all environment variables.
     *
     * @return JsonResponse
     */
    public function getVariables(): JsonResponse
    {
        try {
            $envVars = [];
            
            // Get all environment variables
            foreach ($_ENV as $key => $value) {
                // Skip internal PHP environment variables
                if (str_starts_with($key, 'PHP_')) {
                    continue;
                }
                
                // Determine if the value should be masked
                $isSensitive = $this->isSensitiveVariable($key);
                
                $envVars[$key] = [
                    'key' => $key,
                    'value' => $isSensitive ? '********' : $value,
                    'is_sensitive' => $isSensitive,
                ];
            }
            
            // Sort variables by key
            ksort($envVars);
            
            return $this->success(array_values($envVars));
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve environment variables: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear the environment cache.
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        try {
            Artisan::call('config:clear');
            
            return $this->success('Environment cache cleared successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to clear environment cache: ' . $e->getMessage());
        }
    }
    
    /**
     * Get the .env file content.
     *
     * @return JsonResponse
     */
    public function getEnvFile(): JsonResponse
    {
        try {
            $envPath = base_path('.env');
            
            if (!File::exists($envPath)) {
                return $this->error('Environment file not found.', 404);
            }
            
            $content = File::get($envPath);
            
            return $this->success(['content' => $content]);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve environment file: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the .env file content.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateEnvFile(Request $request): JsonResponse
    {
        try {
            $content = $request->input('content');
            
            if (empty($content)) {
                return $this->error('Content cannot be empty.', 400);
            }
            
            $envPath = base_path('.env');
            
            // Backup the current .env file
            $backupPath = base_path('.env.backup-' . date('Y-m-d-H-i-s'));
            File::copy($envPath, $backupPath);
            
            // Update the .env file
            File::put($envPath, $content);
            
            // Clear the configuration cache
            Artisan::call('config:clear');
            
            return $this->success('Environment file updated successfully.');
        } catch (\Exception $e) {
            return $this->error('Failed to update environment file: ' . $e->getMessage());
        }
    }
    
    /**
     * Determine if a variable is sensitive and should be masked.
     *
     * @param string $key
     * @return bool
     */
    private function isSensitiveVariable(string $key): bool
    {
        $sensitiveKeywords = [
            'key', 'secret', 'password', 'token', 'auth', 'credential', 'private', 'salt',
            'api_key', 'access_key', 'client_secret', 'encryption'
        ];
        
        $key = strtolower($key);
        
        foreach ($sensitiveKeywords as $keyword) {
            if (str_contains($key, $keyword)) {
                return true;
            }
        }
        
        return false;
    }
} 