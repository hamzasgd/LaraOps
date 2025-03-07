<?php

use Hamzasgd\LaravelOps\Http\Controllers\LogViewerController;
use Hamzasgd\LaravelOps\Http\Controllers\ArtisanController;
use Hamzasgd\LaravelOps\Http\Controllers\EnvController;
use Hamzasgd\LaravelOps\Http\Controllers\TinkerController;
use Hamzasgd\LaravelOps\Http\Controllers\SystemInfoController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('laravelops.route_prefix', 'laravelops'),
    'middleware' => config('laravelops.middleware', ['web', 'auth']),
    'as' => 'laravelops.',
], function () {
    // Default route - redirect to dashboard
    Route::get('/', function () {
        return redirect()->route('laravelops.system.index');
    })->name('index');
    
    Route::get('/logs', [LogViewerController::class, 'index'])->name('logs.index');
    Route::get('/logs/{filename}', [LogViewerController::class, 'show'])->name('logs.show');
    Route::get('/logs/live', [LogViewerController::class, 'viewLive'])->name('logs.live');
    
    Route::get('/artisan', [ArtisanController::class, 'index'])->name('artisan.index');
    Route::post('/artisan/execute', [ArtisanController::class, 'execute'])->name('artisan.execute');
    
    Route::get('/env', [EnvController::class, 'index'])->name('env.index');
    Route::post('/env/clear-cache', [EnvController::class, 'clearCache'])->name('env.clear-cache');
    
    Route::get('/tinker', [TinkerController::class, 'index'])->name('tinker.index');
    Route::post('/tinker/execute', [TinkerController::class, 'execute'])->name('tinker.execute');
    Route::get('/tinker/history', [TinkerController::class, 'getHistory'])->name('tinker.history');
    Route::post('/tinker/history', [TinkerController::class, 'saveHistory'])->name('tinker.save-history');

    Route::get('/system', [SystemInfoController::class, 'index'])->name('system.index');
    Route::post('/system/clear-cache', [SystemInfoController::class, 'clearCache'])->name('system.clear-cache');
    Route::post('/system/clear-views', [SystemInfoController::class, 'clearViews'])->name('system.clear-views');
    Route::post('/system/clear-routes', [SystemInfoController::class, 'clearRoutes'])->name('system.clear-routes');
    Route::post('/system/create-link', [SystemInfoController::class, 'createStorageLink'])->name('system.create-link');
    Route::get('/system/resources', [SystemInfoController::class, 'getSystemResources'])->name('system.resources');
}); 