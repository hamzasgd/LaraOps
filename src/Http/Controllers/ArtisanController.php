<?php

namespace Hamzasgd\LaravelOps\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

class ArtisanController extends Controller
{
    public function index()
    {
        $commands = $this->getCommands();
        return view('laravelops::artisan.index', compact('commands'));
    }

    public function execute(Request $request)
    {
        $command = $request->input('command');
        $arguments = $request->input('arguments', []);
        $options = $request->input('options', []);

        // Prepare arguments and options
        $params = [];
        foreach ($arguments as $key => $value) {
            if (!empty($value)) {
                $params[$key] = $value;
            }
        }

        foreach ($options as $key => $value) {
            if ($value === 'on') {
                $params['--' . $key] = true;
            } elseif (!empty($value)) {
                $params['--' . $key] = $value;
            }
        }

        // Capture output
        ob_start();
        try {
            $exitCode = Artisan::call($command, $params);
            $output = Artisan::output();
            $status = $exitCode === 0 ? 'success' : 'error';
        } catch (\Exception $e) {
            $output = $e->getMessage();
            $status = 'error';
        }
        ob_end_clean();

        return response()->json([
            'status' => $status,
            'output' => $output,
        ]);
    }

    private function getCommands()
    {
        $commands = [];
        foreach (Artisan::all() as $name => $command) {
            $description = $command->getDescription();
            
            // Group commands by namespace
            $parts = explode(':', $name);
            $namespace = count($parts) > 1 ? $parts[0] : 'app';
            
            $commands[$namespace][] = [
                'name' => $name,
                'description' => $description,
                'synopsis' => $this->getSynopsis($command),
            ];
        }
        
        // Sort namespaces
        ksort($commands);
        
        return $commands;
    }

    private function getSynopsis(Command $command)
    {
        $definition = $command->getDefinition();
        
        $arguments = [];
        foreach ($definition->getArguments() as $argument) {
            $arguments[] = [
                'name' => $argument->getName(),
                'description' => $argument->getDescription(),
                'required' => $argument->isRequired(),
            ];
        }
        
        $options = [];
        foreach ($definition->getOptions() as $option) {
            $options[] = [
                'name' => $option->getName(),
                'shortcut' => $option->getShortcut(),
                'description' => $option->getDescription(),
                'default' => $option->getDefault(),
                'accepts_value' => $option->acceptValue(),
            ];
        }
        
        return [
            'arguments' => $arguments,
            'options' => $options,
        ];
    }
} 