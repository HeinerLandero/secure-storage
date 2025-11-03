<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', [FileController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// File Management Routes (authenticated users)
Route::middleware('auth')->group(function () {
    // File upload and management
    Route::post('/files/upload', [FileController::class, 'upload'])->name('files.upload');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    // AJAX endpoints for file management
    Route::get('/files/get-files', [FileController::class, 'getFiles'])->name('files.get-files');
    Route::get('/files/storage-info', [FileController::class, 'getStorageInfo'])->name('files.storage-info');

    // Profile management (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes (admin users only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminController::class, 'getStats'])->name('stats');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Group management
    Route::get('/groups', [AdminController::class, 'groups'])->name('groups');
    Route::post('/groups', [AdminController::class, 'storeGroup'])->name('groups.store');
    Route::put('/groups/{group}', [AdminController::class, 'updateGroup'])->name('groups.update');
    Route::delete('/groups/{group}', [AdminController::class, 'destroyGroup'])->name('groups.destroy');

    // Configuration management
    Route::get('/configurations', [AdminController::class, 'configurations'])->name('configurations');
    Route::put('/configurations', [AdminController::class, 'updateConfiguration'])->name('configurations.update');


});

require __DIR__.'/auth.php';

