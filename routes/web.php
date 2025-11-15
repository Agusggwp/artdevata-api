<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use Illuminate\Support\Facades\Route;

// Redirect root
Route::get('/', fn() => redirect()->route('admin.login'));

// === ADMIN AUTH & PANEL ===
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (tanpa middleware guest untuk login)
    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);

    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->name('login')
        ->withoutMiddleware('guest:admin'); // FIX: biar bisa akses login

    Route::post('/login', [AdminAuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/panel', [AdminAuthController::class, 'panel'])->name('panel');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // CRUD
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

        // Chat Admin
       
    });
});

// === API READ-ONLY ===
Route::prefix('api')->group(function () {
    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::get('/portfolios/{id}', [PortfolioController::class, 'show']);

    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    Route::get('/blogs', [BlogController::class, 'index']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);

    // CHAT API
   
});


