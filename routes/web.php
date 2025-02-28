<?php

use Hamzasgd\LaravelOps\Http\Controllers\LogViewerController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => config('laravelops.route_prefix', 'laravelops'),
    'middleware' => config('laravelops.middleware', ['web', 'auth']),
    'as' => 'laravelops.',
], function () {
    Route::get('/logs', [LogViewerController::class, 'index'])->name('logs.index');
    Route::get('/logs/{filename}', [LogViewerController::class, 'show'])->name('logs.show');
}); 