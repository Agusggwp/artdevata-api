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
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\FinanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// === 1. ROOT & ADMIN PANEL ===
Route::redirect('/', '/admin/login', 301);

Route::prefix('admin')->name('admin.')->group(function () {

    // Login & Register (tanpa guest middleware biar bisa buka login meski sudah login)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);

    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/panel', [AdminAuthController::class, 'panel'])->name('panel');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::resource('services', AdminServiceController::class);
        Route::resource('portfolios', AdminPortfolioController::class);
        Route::resource('blogs', AdminBlogController::class);

        // Chat Admin (opsional)
        Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');

        // Salary management
        Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
        Route::post('/salaries/pay', [SalaryController::class, 'pay'])->name('salaries.pay');

        // Finance management
        Route::get('/finance/transactions', [FinanceController::class, 'index'])->name('finance.transactions.index');
        Route::post('/finance/transaction', [FinanceController::class, 'store'])->name('finance.transaction.store');
    });
});

// === 2. PUBLIC API (untuk React/Next.js) – SEMUA DI WEB.PHP ===
Route::prefix('api')->group(function () {

    // Portfolio
    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::get('/portfolios/{id}', [PortfolioController::class, 'show']);

    // Services
    Route::get('/services', [ServiceController::class, 'index']);
   Route::get('/services/{id}', [ServiceController::class, 'show']);

    // Blogs – format wajib { data: [...] } biar frontend langsung jalan
    Route::get('/blogs', function () {
        return response()->json([
            'data' => \App\Models\Blog::latest()->get()->map(fn($blog) => [
                'id'         => $blog->id,
                'title'      => $blog->title,
                'slug'       => $blog->slug ?? Str::slug($blog->title),
                'excerpt'    => $blog->excerpt ?? Str::limit(strip_tags($blog->content), 150),
                'content'    => $blog->content,
                'image'      => $blog->image ? asset('storage/' . $blog->image) : null,
                'category'   => $blog->category ?? 'Umum',
                'author'     => $blog->author ?? 'Tim ArtDevata',
                'created_at' => $blog->created_at,
            ])
        ]);
    });

    Route::get('/blogs/{id}', [BlogController::class, 'show']);

    // Chat API (opsional)
    // Route::post('/chat/send', [ApiChatController::class, 'send']);
});

// === 3. FALLBACK: Jika frontend masih akses langsung /api/blogs tanpa prefix v1 ===
Route::get('/blogs', fn() => redirect('/api/blogs'));

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/panel', [PanelController::class, 'index'])->name('admin.panel');
    Route::resource('/admin/projects', ProjectController::class, ['as' => 'admin']);
    Route::resource('/admin/invoices', InvoiceController::class, ['as' => 'admin']);
    Route::patch('/admin/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('admin.invoices.updateStatus');
});