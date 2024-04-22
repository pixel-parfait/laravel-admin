<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OEmbedController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Login
Route::group(['middleware' => 'guest'], function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::delete('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Users
    Route::resource('users', UserController::class)->except(['show']);

    // Media
    Route::controller(MediaController::class)->group(function () {
        Route::get('library', 'index')->name('library.index');
        Route::post('media', 'store');
        Route::get('media/{media}', 'load');
        Route::get('media/{media}/edit', 'edit')->name('media.edit');
        Route::put('media/{media}', 'update')->name('media.update');
        Route::delete('media/{media}', 'destroy')->name('media.destroy');
        Route::post('upload', 'uploadMultiple')->name('media.upload');
    });

    // Global search
    Route::post('search', [SearchController::class, 'search'])->name('search');

    // Resolve relationships
    Route::post('resolve', [SearchController::class, 'resolve'])->name('resolve');

    // Get iframe from URL
    Route::post('oembed', [OEmbedController::class, 'getOEmbed'])->name('oembed');

    // Clear cache
    Route::delete('cache', [DashboardController::class, 'clearCache'])->name('cache.clear');
});
