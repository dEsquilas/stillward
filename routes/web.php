<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('goals', GoalController::class);
    Route::post('/goals/{goal}/archive', [GoalController::class, 'archive'])->name('goals.archive');
    Route::post('/goals/{goal}/restore', [GoalController::class, 'restore'])->name('goals.restore');
    Route::get('/goals-archived', [GoalController::class, 'archived'])->name('goals.archived');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
