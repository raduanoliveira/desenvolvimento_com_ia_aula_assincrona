<?php

use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskReminderController;
use Illuminate\Support\Facades\Route;

Route::get('/session', [SessionController::class, 'show']);
Route::delete('/session', [SessionController::class, 'destroy']);

Route::middleware('auth')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::patch('/tasks/{task}', [TaskController::class, 'updateTitle']);
    Route::patch('/tasks/{task}/priority', [TaskController::class, 'updatePriority']);
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle']);
    Route::patch('/tasks/{task}/archive', [TaskController::class, 'archive']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::get('/reminders/due-tomorrow', [TaskReminderController::class, 'index']);
});
