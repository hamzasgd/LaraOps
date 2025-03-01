<?php

use Illuminate\Support\Facades\Route;
use Hamzasgd\LaravelOps\Http\Controllers\LaraOpsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// LaraOps routes - serve the SPA for all routes
Route::get('/laravelops', [LaraOpsController::class, 'index'])->name('laravelops.index');
Route::get('/laravelops/{any}', [LaraOpsController::class, 'index'])->where('any', '.*'); 