<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ArtisanController extends ApiController
{
    /**
     * Get all available artisan commands.
     *
     * @return JsonResponse
     */
    public function getCommands(): JsonResponse
    {
        try {
            $commands = [];
            
            // Get all registered commands
            $artisanCommands = Artisan::all();
            
            foreach ($artisanCommands as $name => $command) {
                // Skip hidden commands
                if (str_starts_with($name, '_')) {
                    continue;
                }
                
                // Get command definition
                $definition = $command->getDefinition();
                
                // Get arguments
                $arguments = [];
                foreach ($definition->getArguments() as $argument) {
                    $arguments[] = [
                        'name' => $argument->getName(),
                        'description' => $argument->getDescription(),
                        'required' => $argument->isRequired(),
                        'default' => $argument->getDefault(),
                    ];
                }
                
                // Get options
                $options = [];
                foreach ($definition->getOptions() as $option) {
                    $options[] = [
                        'name' => $option->getName(),
                        'shortcut' => $option->getShortcut(),
                        'description' => $option->getDescription(),
                        'default' => $option->getDefault(),
                        'is_flag' => $option->isFlag(),
                    ];
                }
                
                // Add command to list
                $commands[] = [
                    'name' => $name,
                    'description' => $command->getDescription(),
                    'arguments' => $arguments,
                    'options' => $options,
                    'category' => $this->getCommandCategory($name),
                ];
            }
            
            // Sort commands by category and name
            usort($commands, function ($a, $b) {
                if ($a['category'] === $b['category']) {
                    return strcmp($a['name'], $b['name']);
                }
                return strcmp($a['category'], $b['category']);
            });
            
            return response()->json($commands);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve artisan commands: ' . $e->getMessage());
        }
    }
    
    /**
     * Run an artisan command.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function runCommand(Request $request): JsonResponse
    {
        try {
            $command = $request->input('command');
            $arguments = $request->input('arguments', []);
            $options = $request->input('options', []);
            
            // Validate command
            if (!$command || !Artisan::has($command)) {
                return $this->error('Invalid command', 400);
            }
            
            // Prepare arguments and options
            $params = [];
            
            // Add arguments
            foreach ($arguments as $name => $value) {
                if ($value !== null && $value !== '') {
                    $params[$name] = $value;
                }
            }
            
            // Add options
            foreach ($options as $name => $value) {
                if ($value === true) {
                    // Flag option
                    $params['--' . $name] = true;
                } elseif ($value !== null && $value !== '') {
                    // Value option
                    $params['--' . $name] = $value;
                }
            }
            
            // Capture output
            ob_start();
            $exitCode = Artisan::call($command, $params);
            $output = ob_get_clean() ?: Artisan::output();
            
            // Save command to history
            $this->saveCommandToHistory($command, $params, $output, $exitCode === 0);
            
            return $this->success([
                'success' => $exitCode === 0,
                'output' => $output,
                'exit_code' => $exitCode,
            ]);
        } catch (\Exception $e) {
            return $this->error('Failed to run command: ' . $e->getMessage());
        }
    }
    
    /**
     * Get command history.
     *
     * @return JsonResponse
     */
    public function getHistory(): JsonResponse
    {
        try {
            $this->createHistoryTableIfNotExists();
            
            $history = DB::table('laravelops_artisan_history')
                ->orderBy('executed_at', 'desc')
                ->limit(50)
                ->get();
            
            return response()->json($history);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve command history: ' . $e->getMessage());
        }
    }
    
    /**
     * Save command to history.
     *
     * @param string $command
     * @param array $params
     * @param string $output
     * @param bool $success
     * @return void
     */
    private function saveCommandToHistory(string $command, array $params, string $output, bool $success): void
    {
        try {
            $this->createHistoryTableIfNotExists();
            
            DB::table('laravelops_artisan_history')->insert([
                'command' => $command,
                'parameters' => json_encode($params),
                'output' => $output,
                'success' => $success,
                'executed_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail, don't interrupt the command execution
            logger()->error('Failed to save artisan command to history: ' . $e->getMessage());
        }
    }
    
    /**
     * Create history table if it doesn't exist.
     *
     * @return void
     */
    private function createHistoryTableIfNotExists(): void
    {
        if (!Schema::hasTable('laravelops_artisan_history')) {
            Schema::create('laravelops_artisan_history', function ($table) {
                $table->id();
                $table->string('command');
                $table->text('parameters')->nullable();
                $table->longText('output')->nullable();
                $table->boolean('success')->default(true);
                $table->timestamp('executed_at');
            });
        }
    }
    
    /**
     * Get the category of a command based on its name.
     *
     * @param string $name
     * @return string
     */
    private function getCommandCategory(string $name): string
    {
        $categories = [
            'cache' => 'Cache',
            'config' => 'Configuration',
            'db' => 'Database',
            'migrate' => 'Database',
            'make' => 'Generator',
            'vendor:publish' => 'Package',
            'queue' => 'Queue',
            'route' => 'Routing',
            'schedule' => 'Scheduling',
            'storage' => 'Storage',
            'view' => 'View',
        ];
        
        foreach ($categories as $prefix => $category) {
            if (str_starts_with($name, $prefix)) {
                return $category;
            }
        }
        
        return 'Other';
    }
} 