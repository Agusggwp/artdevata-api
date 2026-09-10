<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminServiceController;
use App\Http\Controllers\AdminPortfolioController;
use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ClientController as ApiClientController;
use App\Http\Controllers\Api\ChatController as ApiChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DocumentationController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LoginHistoryController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AccountSecurityController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\QuotationController;
use App\Http\Controllers\Admin\ProjectTaskController;
use App\Http\Controllers\Admin\ProjectDocumentController;
use App\Http\Controllers\Admin\GlobalSearchController;
use App\Http\Controllers\Api\DocumentationController as ApiDocumentationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// === 1. ROOT & ADMIN PANEL ===
Route::redirect('/', '/admin/login', 301);

Route::prefix('admin')->name('admin.')->group(function () {

    // Login & Register (Guest / Auth)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::get('/register', [AdminAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AdminAuthController::class, 'register']);

    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/panel', [PanelController::class, 'index'])->name('panel');
        Route::get('/admin/panel', [PanelController::class, 'index'])->name('panel.alias');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Global Search API (Ctrl + K)
        Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global-search');
        Route::post('/notifications/mark-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-read');

        // My Account Security (Accessible by all logged in admins)
        Route::get('/account/security', [AccountSecurityController::class, 'show'])->name('account.security');
        Route::put('/account/security/password', [AccountSecurityController::class, 'updatePassword'])->name('account.security.update-password');
        Route::post('/account/security/logout-others', [AccountSecurityController::class, 'logoutOtherSessions'])->name('account.security.logout-others');

        // CRM / Leads Management
        Route::middleware('permission:leads.view')->group(function () {
            Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
            Route::resource('leads', LeadController::class);
        });

        // Quotations Management
        Route::middleware('permission:quotations.view')->group(function () {
            Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
            Route::get('/quotations/{quotation}/create-project', [QuotationController::class, 'createProject'])->name('quotations.create-project');
            Route::resource('quotations', QuotationController::class);
        });

        // Projects Management 2.0
        Route::middleware('permission:projects.view')->group(function () {
            Route::get('/projects/kanban', [ProjectController::class, 'kanban'])->name('projects.kanban');
            Route::post('/projects/{project}/update-status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
            Route::post('/projects/{project}/tasks', [ProjectTaskController::class, 'store'])->name('projects.tasks.store');
            Route::put('/tasks/{task}', [ProjectTaskController::class, 'update'])->name('projects.tasks.update');
            Route::delete('/tasks/{task}', [ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy');
            Route::post('/projects/{project}/documents', [ProjectDocumentController::class, 'store'])->name('projects.documents.store');
            Route::get('/documents/{document}/download', [ProjectDocumentController::class, 'download'])->name('projects.documents.download');
            Route::delete('/documents/{document}', [ProjectDocumentController::class, 'destroy'])->name('projects.documents.destroy');
            Route::resource('projects', ProjectController::class);
        });

        // Services
        Route::middleware('permission:services.view')->group(function () {
            Route::resource('services', AdminServiceController::class);
        });

        // Portfolios
        Route::middleware('permission:portfolios.view')->group(function () {
            Route::resource('portfolios', AdminPortfolioController::class);
        });

        // Blogs
        Route::middleware('permission:blogs.view')->group(function () {
            Route::resource('blogs', AdminBlogController::class);
        });

        // Salary management
        Route::middleware('permission:salaries.view')->group(function () {
            Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries.index');
            Route::post('/salaries/pay', [SalaryController::class, 'pay'])->name('salaries.pay');
        });

        // Finance management
        Route::middleware('permission:finance.view')->group(function () {
            Route::get('/finance/transactions', [FinanceController::class, 'index'])->name('finance.transactions.index');
            Route::post('/finance/transaction', [FinanceController::class, 'store'])->name('finance.transaction.store');
        });

        // Client management
        Route::middleware('permission:clients.view')->group(function () {
            Route::resource('clients', ClientController::class);
        });

        // User / Admin management
        Route::middleware('permission:admins.view')->group(function () {
            Route::post('/users/{user}/force-logout', [UserController::class, 'forceLogout'])->name('users.force-logout');
            Route::resource('users', UserController::class);
        });

        // Documentation management
        Route::middleware('permission:documentations.view')->group(function () {
            Route::resource('documentations', DocumentationController::class);
        });

        // Roles & Permissions management
        Route::middleware('permission:roles.manage')->group(function () {
            Route::resource('roles', RoleController::class);
        });

        // Centralized Audit Logs
        Route::middleware('permission:activity_logs.view')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        });

        // Login Histories
        Route::middleware('permission:login_history.view')->group(function () {
            Route::get('/login-histories', [LoginHistoryController::class, 'index'])->name('login-histories.index');
        });

        // Security Dashboard & Hardening Overview
        Route::middleware('permission:security.view')->group(function () {
            Route::get('/security', [SecurityController::class, 'index'])->name('security.index');
        });

        // Invoices Management
        Route::resource('invoices', InvoiceController::class);
        Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
    });
});

// === 2. PUBLIC API (untuk React/Next.js) – RATE LIMITED (60 req/min) ===
Route::prefix('api')->middleware('throttle:60,1')->group(function () {

    // Portfolio
    Route::get('/portfolios', [PortfolioController::class, 'index']);
    Route::get('/portfolios/{id}', [PortfolioController::class, 'show']);

    // Services
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    // Blogs
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

    // Clients
    Route::get('/clients', [ApiClientController::class, 'index']);

    // Documentations
    Route::get('/documentations', [ApiDocumentationController::class, 'index']);
    Route::get('/documentations/{id}', [ApiDocumentationController::class, 'show']);

    // Forward-compatible API Versioning aliases (/api/v1/*)
    Route::prefix('v1')->group(function () {
        Route::get('/portfolios', [PortfolioController::class, 'index']);
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/clients', [ApiClientController::class, 'index']);
        Route::get('/documentations', [ApiDocumentationController::class, 'index']);
    });
});

// === 3. FALLBACK ===
Route::get('/blogs', fn() => redirect('/api/blogs'));