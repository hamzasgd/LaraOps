<?php

namespace Hamzasgd\LaravelOps;

use Illuminate\Support\ServiceProvider;

class LaravelOpsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravelops');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravelops.php' => config_path('laravelops.php'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravelops'),
            ], 'laravelops');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravelops.php', 'laravelops');
    }
} 