<?php

return [
    // Route prefix for all LaravelOps routes
    'route_prefix' => 'laravelops',

    // Middleware to protect the routes
    'middleware' => ['web', 'auth', 'laravelops.access'],

    // Log viewer settings
    'logs' => [
        // Maximum file size to display (in MB)
        'max_file_size' => 50,
        
        // Number of log entries to show per page
        'per_page' => 100,
    ],
    
    // Artisan UI settings
    'artisan' => [
        // Whether to enable the Artisan UI
        'enabled' => true,
        
        // Commands to exclude from the UI
        'excluded_commands' => [
            // Add any commands you want to exclude
        ],
    ],
]; 