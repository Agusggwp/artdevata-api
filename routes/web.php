<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminBlogController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// ADMIN AUTH & PANEL
Route::prefix('admin')->name('admin.')->group(function () {

    // === AUTH ===
    Route::get('/register', [AdminAuthController::class, 'showRegister'])
        ->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);

    // TAMBAHKAN .withoutMiddleware('guest:admin') DI SINI
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('login')
        ->middleware('guest:admin')
        ->withoutMiddleware('guest:admin'); // ← BARIS INI MEMPERBAIKI SEMUA

    Route::post('/login', [AdminAuthController::class, 'login']);

    // === PROTECTED ROUTES (hanya admin login) ===
    Route::middleware('auth:admin')->group(function () {

        // Panel Utama
        Route::get('/panel', [AdminAuthController::class, 'panel'])
            ->name('panel');

        // Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])
            ->name('logout');

        // === CRUD FITUR ===
        Route::resource('services', AdminServiceController::class)->names([
            'index' => 'services.index',
            'create' => 'services.create',
            'store' => 'services.store',
            'edit' => 'services.edit',
            'update' => 'services.update',
            'destroy' => 'services.destroy',
        ]);

        Route::resource('portfolios', AdminPortfolioController::class)->names([
            'index' => 'portfolios.index',
            'create' => 'portfolios.create',
            'store' => 'portfolios.store',
            'edit' => 'portfolios.edit',
            'update' => 'portfolios.update',
            'destroy' => 'portfolios.destroy',
        ]);

        Route::resource('blogs', AdminBlogController::class)->names([
            'index' => 'blogs.index',
            'create' => 'blogs.create',
            'store' => 'blogs.store',
            'edit' => 'blogs.edit',
            'update' => 'blogs.update',
            'destroy' => 'blogs.destroy',
        ]);
    });
});