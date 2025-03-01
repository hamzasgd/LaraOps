<?php

namespace Hamzasgd\LaravelOps;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class LaravelOpsServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravelops');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Register middleware
        $router->aliasMiddleware('laravelops.access', \Hamzasgd\LaravelOps\Http\Middleware\AccessMiddleware::class);

        if ($this->app->runningInConsole()) {
            // Publish configuration
            $this->publishes([
                __DIR__.'/../config/laravelops.php' => config_path('laravelops.php'),
            ], 'laravelops-config');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravelops'),
            ], 'laravelops-views');

            // Publish assets
            $this->publishes([
                __DIR__.'/../public/build' => public_path('vendor/laravelops'),
            ], 'laravelops-assets');

            // Publish all resources
            $this->publishes([
                __DIR__.'/../config/laravelops.php' => config_path('laravelops.php'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravelops'),
                __DIR__.'/../public/build' => public_path('vendor/laravelops'),
            ], 'laravelops');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelops.php', 'laravelops');
    }
} 