<?php

namespace Hamzasgd\LaravelOps\Http\Controllers\Api;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use Symfony\Component\Process\Process;

class ScheduleController extends ApiController
{
    /**
     * Get all scheduled tasks.
     *
     * @return JsonResponse
     */
    public function getTasks(): JsonResponse
    {
        try {
            $schedule = App::make(Schedule::class);
            $events = $this->getScheduledEvents($schedule);
            
            return $this->success($events);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve scheduled tasks: ' . $e->getMessage());
        }
    }
    
    /**
     * Run a scheduled task.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function runTask(Request $request): JsonResponse
    {
        try {
            $command = $request->input('command');
            
            if (empty($command)) {
                return $this->error('Command cannot be empty.', 400);
            }
            
            // Check if it's an artisan command
            if (str_starts_with($command, 'php artisan ')) {
                $artisanCommand = substr($command, 12); // Remove 'php artisan ' prefix
                
                // Split the command and arguments
                $parts = explode(' ', $artisanCommand);
                $commandName = array_shift($parts);
                
                // Prepare arguments
                $arguments = [];
                foreach ($parts as $part) {
                    if (str_starts_with($part, '--')) {
                        $option = substr($part, 2);
                        if (str_contains($option, '=')) {
                            [$key, $value] = explode('=', $option, 2);
                            $arguments['--' . $key] = $value;
                        } else {
                            $arguments['--' . $option] = true;
                        }
                    } elseif (str_starts_with($part, '-')) {
                        $option = substr($part, 1);
                        $arguments['-' . $option] = true;
                    } else {
                        $arguments[] = $part;
                    }
                }
                
                // Run the artisan command
                ob_start();
                $exitCode = Artisan::call($commandName, $arguments);
                $output = ob_get_clean() ?: Artisan::output();
                
                return $this->success([
                    'success' => $exitCode === 0,
                    'output' => $output,
                    'exit_code' => $exitCode,
                ]);
            } else {
                // Run as shell command
                $process = Process::fromShellCommandline($command);
                $process->setTimeout(60); // 60 seconds timeout
                $process->run();
                
                return $this->success([
                    'success' => $process->isSuccessful(),
                    'output' => $process->getOutput() ?: $process->getErrorOutput(),
                    'exit_code' => $process->getExitCode(),
                ]);
            }
        } catch (\Exception $e) {
            return $this->error('Failed to run scheduled task: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all scheduled events from the schedule.
     *
     * @param Schedule $schedule
     * @return array
     */
    private function getScheduledEvents(Schedule $schedule): array
    {
        $events = [];
        $reflection = new \ReflectionClass($schedule);
        $eventsProperty = $reflection->getProperty('events');
        $eventsProperty->setAccessible(true);
        $scheduledEvents = $eventsProperty->getValue($schedule);
        
        foreach ($scheduledEvents as $event) {
            $eventReflection = new \ReflectionClass($event);
            
            // Get command
            $commandProperty = $eventReflection->getProperty('command');
            $commandProperty->setAccessible(true);
            $command = $commandProperty->getValue($event);
            
            // Get expression
            $expressionProperty = $eventReflection->getProperty('expression');
            $expressionProperty->setAccessible(true);
            $expression = $expressionProperty->getValue($event);
            
            // Get description
            $descriptionProperty = $eventReflection->getProperty('description');
            $descriptionProperty->setAccessible(true);
            $description = $descriptionProperty->getValue($event) ?: 'No description';
            
            // Get timezone
            $timezoneProperty = $eventReflection->getProperty('timezone');
            $timezoneProperty->setAccessible(true);
            $timezone = $timezoneProperty->getValue($event);
            
            // Get without overlapping
            $withoutOverlappingProperty = $eventReflection->getProperty('withoutOverlapping');
            $withoutOverlappingProperty->setAccessible(true);
            $withoutOverlapping = $withoutOverlappingProperty->getValue($event);
            
            // Get run in background
            $runInBackgroundProperty = $eventReflection->getProperty('runInBackground');
            $runInBackgroundProperty->setAccessible(true);
            $runInBackground = $runInBackgroundProperty->getValue($event);
            
            // Get next due date
            $nextRunDate = $this->getNextRunDate($expression, $timezone);
            
            // Add to events array
            $events[] = [
                'command' => $command,
                'expression' => $expression,
                'description' => $description,
                'timezone' => $timezone ? $timezone->getName() : config('app.timezone'),
                'without_overlapping' => $withoutOverlapping,
                'run_in_background' => $runInBackground,
                'next_run' => $nextRunDate,
                'human_readable' => $this->getHumanReadableExpression($expression),
            ];
        }
        
        // Sort by next run date
        usort($events, function ($a, $b) {
            return strtotime($a['next_run']) - strtotime($b['next_run']);
        });
        
        return $events;
    }
    
    /**
     * Get the next run date for a cron expression.
     *
     * @param string $expression
     * @param \DateTimeZone|string|null $timezone
     * @return string
     */
    private function getNextRunDate(string $expression, $timezone = null): string
    {
        $cron = new \Cron\CronExpression($expression);
        $timezone = $timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone($timezone ?: config('app.timezone'));
        
        return $cron->getNextRunDate()->setTimezone($timezone)->format('Y-m-d H:i:s');
    }
    
    /**
     * Get a human-readable description of a cron expression.
     *
     * @param string $expression
     * @return string
     */
    private function getHumanReadableExpression(string $expression): string
    {
        try {
            $translator = new \Lorisleiva\CronTranslator\CronTranslator();
            return $translator->translate($expression);
        } catch (\Exception $e) {
            // If the package is not available or there's an error, return a basic description
            $parts = explode(' ', $expression);
            
            if (count($parts) !== 5) {
                return 'Custom schedule';
            }
            
            [$minute, $hour, $day, $month, $weekday] = $parts;
            
            if ($minute === '*' && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
                return 'Every minute';
            }
            
            if ($minute === '0' && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
                return 'Hourly';
            }
            
            if ($minute === '0' && $hour === '0' && $day === '*' && $month === '*' && $weekday === '*') {
                return 'Daily at midnight';
            }
            
            if ($minute === '0' && $hour === '0' && $day === '*' && $month === '*' && $weekday === '0') {
                return 'Weekly on Sunday at midnight';
            }
            
            if ($minute === '0' && $hour === '0' && $day === '1' && $month === '*' && $weekday === '*') {
                return 'Monthly on the 1st at midnight';
            }
            
            return 'Custom schedule: ' . $expression;
        }
    }
} 