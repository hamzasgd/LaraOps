<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

class TinkerController extends ApiController
{
    /**
     * Execute PHP code using Tinker.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function execute(Request $request): JsonResponse
    {
        try {
            $code = $request->input('code');
            
            if (empty($code)) {
                return $this->error('Code cannot be empty.', 400);
            }
            
            // Check for potentially dangerous code
            if ($this->containsDangerousCode($code)) {
                return $this->error('The code contains potentially dangerous operations.', 403);
            }
            
            // Create a temporary file with the code
            $tempFile = tempnam(sys_get_temp_dir(), 'tinker_');
            file_put_contents($tempFile, $this->prepareTinkerCode($code));
            
            // Execute the code using Tinker
            $process = new Process([
                'php', 
                base_path('artisan'), 
                'tinker', 
                '--execute', 
                "require '{$tempFile}';"
            ]);
            
            $process->setTimeout(30); // 30 seconds timeout
            $process->run();
            
            // Clean up the temporary file
            @unlink($tempFile);
            
            // Save to history
            $this->saveToHistory($code, $process->getOutput(), $process->isSuccessful());
            
            if (!$process->isSuccessful()) {
                return $this->error('Execution failed: ' . $process->getErrorOutput());
            }
            
            return $this->success([
                'output' => $process->getOutput(),
                'execution_time' => $process->getExitCode() === 0 ? $process->getExitCode() : null,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to execute code: ' . $e->getMessage());
        }
    }
    
    /**
     * Get execution history.
     *
     * @return JsonResponse
     */
    public function getHistory(): JsonResponse
    {
        try {
            $this->createHistoryTableIfNotExists();
            
            $history = DB::table('laravelops_tinker_history')
                ->orderBy('executed_at', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json($history);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve execution history: ' . $e->getMessage());
        }
    }
    
    /**
     * Save execution to history.
     *
     * @param string $code
     * @param string $output
     * @param bool $success
     * @return void
     */
    private function saveToHistory(string $code, string $output, bool $success): void
    {
        try {
            $this->createHistoryTableIfNotExists();
            
            DB::table('laravelops_tinker_history')->insert([
                'code' => $code,
                'output' => $output,
                'success' => $success,
                'executed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail, don't interrupt the execution
            logger()->error('Failed to save tinker execution to history: ' . $e->getMessage());
        }
    }
    
    /**
     * Create history table if it doesn't exist.
     *
     * @return void
     */
    private function createHistoryTableIfNotExists(): void
    {
        if (!Schema::hasTable('laravelops_tinker_history')) {
            Schema::create('laravelops_tinker_history', function ($table) {
                $table->id();
                $table->text('code');
                $table->longText('output')->nullable();
                $table->boolean('success')->default(true);
                $table->timestamp('executed_at');
            });
        }
    }
    
    /**
     * Prepare code for Tinker execution.
     *
     * @param string $code
     * @return string
     */
    private function prepareTinkerCode(string $code): string
    {
        // Wrap code in a try-catch block to capture errors
        return <<<PHP
try {
    // Start output buffering to capture all output
    ob_start();
    
    // Execute the code
    {$code}
    
    // Get the output
    \$output = ob_get_clean();
    
    // Print the output
    echo \$output;
} catch (\Throwable \$e) {
    // Get any buffered output
    \$output = ob_get_clean();
    
    // Print the output and the error
    echo \$output;
    echo "Error: " . \$e->getMessage() . " in " . \$e->getFile() . " on line " . \$e->getLine() . "\\n";
    echo \$e->getTraceAsString();
}
PHP;
    }
    
    /**
     * Check if the code contains potentially dangerous operations.
     *
     * @param string $code
     * @return bool
     */
    private function containsDangerousCode(string $code): bool
    {
        $dangerousFunctions = [
            'system', 'exec', 'shell_exec', 'passthru', 'proc_open', 'popen',
            'pcntl_exec', 'eval', 'assert', 'create_function', 'unlink', 'rmdir',
            'file_put_contents', 'file_get_contents', 'copy', 'rename', 'chmod',
            'chown', 'touch', 'symlink', 'link', 'mkdir', 'glob', 'scandir',
            'opendir', 'readdir', 'dir', 'DirectoryIterator', 'RecursiveDirectoryIterator',
            'FilesystemIterator', 'GlobIterator', 'SplFileObject', 'fopen', 'tmpfile',
            'tempnam', 'parse_ini_file', 'parse_ini_string', 'dl', 'preg_replace',
            'create_function', 'call_user_func', 'call_user_func_array', 'register_shutdown_function',
            'register_tick_function', 'highlight_file', 'show_source', 'php_strip_whitespace',
            'get_meta_tags', 'get_headers', 'getallheaders', 'get_browser', 'socket_create',
            'socket_connect', 'socket_write', 'socket_send', 'socket_recv', 'socket_read',
            'fsockopen', 'pfsockopen', 'stream_socket_client', 'stream_socket_server',
            'stream_socket_accept', 'stream_socket_pair', 'stream_get_contents',
            'stream_get_line', 'stream_get_meta_data', 'stream_set_timeout',
            'stream_set_blocking', 'stream_set_write_buffer', 'stream_set_read_buffer',
            'stream_select', 'stream_context_create', 'stream_context_set_option',
            'stream_context_set_params', 'stream_context_get_options',
            'stream_context_get_params', 'stream_filter_prepend', 'stream_filter_append',
            'stream_filter_remove', 'stream_wrapper_register', 'stream_wrapper_unregister',
            'stream_wrapper_restore', 'stream_copy_to_stream', 'stream_bucket_make_writeable',
            'stream_bucket_prepend', 'stream_bucket_append', 'stream_bucket_new',
            'output_add_rewrite_var', 'output_reset_rewrite_vars', 'posix_kill',
            'posix_mkfifo', 'posix_setpgid', 'posix_setsid', 'posix_setuid',
            'posix_setgid', 'posix_seteuid', 'posix_setegid', 'posix_uname',
            'pcntl_alarm', 'pcntl_exec', 'pcntl_fork', 'pcntl_getpriority',
            'pcntl_setpriority', 'pcntl_signal', 'pcntl_signal_dispatch',
            'pcntl_sigprocmask', 'pcntl_sigtimedwait', 'pcntl_sigwaitinfo',
            'pcntl_wait', 'pcntl_waitpid', 'pcntl_wexitstatus', 'pcntl_wifexited',
            'pcntl_wifsignaled', 'pcntl_wifstopped', 'pcntl_wstopsig', 'pcntl_wtermsig',
        ];
        
        // Check for dangerous functions
        foreach ($dangerousFunctions as $function) {
            if (preg_match('/\b' . preg_quote($function, '/') . '\s*\(/i', $code)) {
                return true;
            }
        }
        
        // Check for backtick operator
        if (preg_match('/`.*`/', $code)) {
            return true;
        }
        
        return false;
    }
} 