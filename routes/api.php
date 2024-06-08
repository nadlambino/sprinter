<?php

use App\Http\Controllers\Api\v1\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::name('api')->apiResource('/tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);
    // Route::get('/tasks', [TaskController::class, 'index'])->name('api.tasks.index');
    // Route::post('/tasks/store', [TaskController::class, 'store'])->name('api.tasks.store');
    // Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('api.tasks.update');
});