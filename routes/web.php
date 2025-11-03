<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminFileController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Homepage with upload form
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/upload', [HomeController::class, 'upload'])->name('upload');

// File access routes
Route::get('/f/{short_code}', [FileController::class, 'access'])->name('file.access');
Route::post('/f/{short_code}/verify', [FileController::class, 'verify'])->name('file.verify');
Route::get('/f/{short_code}/preview', [FileController::class, 'preview'])->name('file.preview');
Route::get('/f/{short_code}/download', [FileController::class, 'download'])->name('file.download');

/*
|--------------------------------------------------------------------------
| Admin Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // File Management
    Route::get('/files', [AdminFileController::class, 'index'])->name('files.index');
    Route::post('/files/bulk-delete', [AdminFileController::class, 'bulkDelete'])->name('files.bulk-delete');
    Route::get('/files/{file}', [AdminFileController::class, 'show'])->name('files.show');
    Route::delete('/files/{file}', [AdminFileController::class, 'destroy'])->name('files.destroy');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/downloads', [AnalyticsController::class, 'downloads'])->name('analytics.downloads');
    Route::get('/analytics/uploads', [AnalyticsController::class, 'uploads'])->name('analytics.uploads');

    // Activity Logs
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
