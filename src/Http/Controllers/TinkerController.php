<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TinkerController extends Controller
{
    public function index()
    {
        return view('laravelops::tinker.index');
    }
    
    public function execute(Request $request)
    {
        $code = $request->input('code');
        
        if (empty($code)) {
            return response()->json([
                'status' => 'error',
                'output' => 'No code provided.'
            ]);
        }
        
        // Create a temporary file with the code
        $tempFile = $this->createTempFile($code);
        
        try {
            // Execute the code using Artisan Tinker
            $output = $this->executeTinkerCode($tempFile);
            
            return response()->json([
                'status' => 'success',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'output' => 'Error: ' . $e->getMessage()
            ]);
        } finally {
            // Clean up the temporary file
            if (File::exists($tempFile)) {
                File::delete($tempFile);
            }
        }
    }
    
    private function createTempFile($code)
    {
        $tempDir = storage_path('app/laravelops/tinker');
        
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }
        
        $filename = $tempDir . '/' . Str::random(16) . '.php';
        
        // Wrap the code to capture output
        $wrappedCode = <<<EOT
<?php
\$output = function() {
    ob_start();
    try {
        $code
    } catch (\Exception \$e) {
        echo "Error: " . \$e->getMessage();
    }
    return ob_get_clean();
};

echo \$output();
EOT;
        
        File::put($filename, $wrappedCode);
        
        return $filename;
    }
    
    private function executeTinkerCode($tempFile)
    {
        $basePath = base_path();
        $relativePath = str_replace($basePath . '/', '', $tempFile);
        
        // Create a process to run the code through Artisan Tinker
        $process = new Process([
            PHP_BINARY,
            $basePath . '/artisan',
            'tinker',
            '--execute',
            "require '$relativePath';"
        ]);
        
        $process->setWorkingDirectory($basePath);
        $process->setTimeout(30); // 30 seconds timeout
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }
        
        return trim($process->getOutput());
    }
    
    public function getHistory()
    {
        $historyFile = storage_path('app/laravelops/tinker_history.json');
        
        if (File::exists($historyFile)) {
            $history = json_decode(File::get($historyFile), true);
        } else {
            $history = [];
        }
        
        return response()->json($history);
    }
    
    public function saveHistory(Request $request)
    {
        $code = $request->input('code');
        $historyFile = storage_path('app/laravelops/tinker_history.json');
        
        if (File::exists($historyFile)) {
            $history = json_decode(File::get($historyFile), true);
        } else {
            $history = [];
        }
        
        // Add new code to history (avoid duplicates)
        if (!in_array($code, $history)) {
            // Limit history to 50 items
            array_unshift($history, $code);
            $history = array_slice($history, 0, 50);
            
            File::put($historyFile, json_encode($history));
        }
        
        return response()->json(['status' => 'success']);
    }
} 