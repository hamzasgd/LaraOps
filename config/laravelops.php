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
    
    // Environment variables settings
    'env' => [
        // Whether to enable the Environment UI
        'enabled' => true,
        
        // Variables to hide completely (won't be shown at all)
        'hidden_variables' => [
            // Add any variables you want to hide completely
        ],
        
        // Variables to mask (will show as ********)
        'masked_variables' => [
            'DB_PASSWORD',
            'REDIS_PASSWORD',
            'MAIL_PASSWORD',
            'AWS_SECRET',
            // Add any other sensitive variables
        ],
    ],
]; 