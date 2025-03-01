<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Hamzasgd\LaravelOps\Http\Controllers\Api\LogViewerController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\ArtisanController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\EnvController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\TinkerController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\ScheduleController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\SystemInfoController;
use Hamzasgd\LaravelOps\Http\Controllers\Api\EnvironmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// LaraOps API routes
Route::prefix('laravelops')->middleware(['api'])->group(function () {
    // System info
    Route::get('/system/info', [SystemInfoController::class, 'getInfo']);
    Route::get('/system/resources', [SystemInfoController::class, 'getResources']);
    Route::post('/system/clear-cache', [SystemInfoController::class, 'clearCache']);
    Route::post('/system/clear-views', [SystemInfoController::class, 'clearViews']);
    Route::post('/system/clear-routes', [SystemInfoController::class, 'clearRoutes']);
    Route::post('/system/storage-link', [SystemInfoController::class, 'createStorageLink']);
    
    // Logs
    Route::get('/logs', [LogViewerController::class, 'getLogs']);
    Route::get('/logs/{filename}', [LogViewerController::class, 'getLogContent']);
    Route::delete('/logs/{filename}', [LogViewerController::class, 'deleteLog']);
    
    // Artisan
    Route::get('/artisan', [ArtisanController::class, 'getCommands']);
    Route::post('/artisan/run', [ArtisanController::class, 'runCommand']);
    Route::get('/artisan/history', [ArtisanController::class, 'getHistory']);
    
    // Environment
    Route::get('/env', [EnvironmentController::class, 'getVariables']);
    Route::post('/env/clear-cache', [EnvironmentController::class, 'clearCache']);
    Route::get('/env/file', [EnvironmentController::class, 'getEnvFile']);
    Route::post('/env/file', [EnvironmentController::class, 'updateEnvFile']);
    
    // Tinker
    Route::post('/tinker/execute', [TinkerController::class, 'execute']);
    Route::get('/tinker/history', [TinkerController::class, 'getHistory']);
    
    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'getTasks']);
    Route::post('/schedule/run', [ScheduleController::class, 'runTask']);
}); 