<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Task CRUD
    Route::get('/tasks/dates', [TaskController::class, 'dates']);
    Route::post('/tasks/reorder', [TaskController::class, 'reorder']);
    Route::apiResource('tasks', TaskController::class);
});
