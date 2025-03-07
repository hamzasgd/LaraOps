<?php

namespace Hamzasgd\LaravelOps;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Hamzasgd\LaravelOps\Http\Controllers\LogViewerController;

class LaravelOpsServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravelops');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        // Register middleware
        $router->aliasMiddleware('laravelops.access', \Hamzasgd\LaravelOps\Http\Middleware\AccessMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravelops.php' => config_path('laravelops.php'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravelops'),
            ], 'laravelops');
        }

        // Register view composers
        view()->composer('laravelops::logs.live', function ($view) {
            $view->with('formatStackTrace', function ($stackTrace) {
                return app(LogViewerController::class)->formatStackTrace($stackTrace);
            });
        });
        
        view()->composer('laravelops::logs.show', function ($view) {
            $view->with('formatStackTrace', function ($stackTrace) {
                return app(LogViewerController::class)->formatStackTrace($stackTrace);
            });
        });
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelops.php', 'laravelops');
    }
} 