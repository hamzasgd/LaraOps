<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;

class EnvController extends Controller
{
    public function index()
    {
        $envVariables = $this->getEnvVariables();
        
        return view('laravelops::env.index', [
            'envVariables' => $envVariables
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
} 