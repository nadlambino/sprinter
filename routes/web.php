<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PriorityLevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

const WEB_METHODS = ['index', 'show', 'create', 'edit'];

Route::get('/', fn () => Inertia::render('Welcome', ['canLogin' => Route::has('login'), 'canRegister' => Route::has('register')]));

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/tasks/drafts', [TaskController::class, 'drafts'])->name('tasks.drafts');
    Route::get('/tasks/trashed', [TaskController::class, 'trashed'])->name('tasks.trashed');
    Route::resource('/tasks', TaskController::class)->only(WEB_METHODS);

    Route::get('/statuses/trashed', [StatusController::class, 'trashed'])->name('statuses.trashed');
    Route::resource('/statuses', StatusController::class)->only(WEB_METHODS);

    Route::resource('/priority-levels', PriorityLevelController::class)->only(WEB_METHODS);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
