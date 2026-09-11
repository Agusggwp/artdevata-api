<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthController;
use App\Http\Controllers\Api\v1\Public\PublicContentController;
use App\Http\Controllers\Api\v1\Admin\DashboardController;
use App\Http\Controllers\Api\v1\Admin\LeadController;
use App\Http\Controllers\Api\v1\Admin\QuotationController;
use App\Http\Controllers\Api\v1\Admin\ProjectController;
use App\Http\Controllers\Api\v1\Admin\ProjectTaskController;
use App\Http\Controllers\Api\v1\Admin\ProjectDocumentController;
use App\Http\Controllers\Api\v1\Admin\InvoiceController;
use App\Http\Controllers\Api\v1\Admin\ClientController;
use App\Http\Controllers\Api\v1\Admin\ServiceController;
use App\Http\Controllers\Api\v1\Admin\PortfolioController;
use App\Http\Controllers\Api\v1\Admin\BlogController;
use App\Http\Controllers\Api\v1\Admin\SalaryController;
use App\Http\Controllers\Api\v1\Admin\FinanceController;
use App\Http\Controllers\Api\v1\Admin\UserController;
use App\Http\Controllers\Api\v1\Admin\RoleController;
use App\Http\Controllers\Api\v1\Admin\DocumentationController;
use App\Http\Controllers\Api\v1\Admin\AuditController;
use App\Http\Controllers\Api\v1\Admin\NotificationController;
use App\Http\Controllers\Api\v1\Admin\GlobalSearchController;

/*
|--------------------------------------------------------------------------
| API Routes V1 - ArtDevata Backend
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {

    // ==========================================
    // 1. PUBLIC ENDPOINTS (No Authentication Required)
    // ==========================================
    Route::prefix('public')->group(function () {
        Route::get('/services', [PublicContentController::class, 'services']);
        Route::get('/services/{id}', [PublicContentController::class, 'serviceShow']);

        Route::get('/portfolios', [PublicContentController::class, 'portfolios']);
        Route::get('/portfolios/{id}', [PublicContentController::class, 'portfolioShow']);

        Route::get('/blogs', [PublicContentController::class, 'blogs']);
        Route::get('/blogs/{idOrSlug}', [PublicContentController::class, 'blogShow']);

        Route::get('/clients', [PublicContentController::class, 'clients']);
        Route::get('/documentations', [PublicContentController::class, 'documentations']);

        // Form Kontak & Inquiry Prospek Baru
        Route::post('/contact', [PublicContentController::class, 'submitLead']);
        Route::post('/leads', [PublicContentController::class, 'submitLead']);
    });

    // ==========================================
    // 2. AUTHENTICATION ENDPOINTS
    // ==========================================
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });

    // ==========================================
    // 3. PROTECTED ADMIN & MANAGEMENT API (Sanctum Auth Required)
    // ==========================================
    Route::prefix('admin')->middleware('auth:sanctum')->group(function () {

        // Dashboard Metrics & Universal Search
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/global-search', [GlobalSearchController::class, 'search']);

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/mark-read', [NotificationController::class, 'markAllAsRead']);

        // CRM & Leads
        Route::post('/leads/{id}/convert', [LeadController::class, 'convert']);
        Route::apiResource('leads', LeadController::class);

        // Quotations
        Route::post('/quotations/{id}/create-project', [QuotationController::class, 'createProject']);
        Route::apiResource('quotations', QuotationController::class);

        // Projects & Kanban
        Route::get('/projects/kanban', [ProjectController::class, 'kanban']);
        Route::post('/projects/{id}/update-status', [ProjectController::class, 'updateStatus']);
        Route::post('/projects/{projectId}/tasks', [ProjectTaskController::class, 'store']);
        Route::put('/tasks/{id}', [ProjectTaskController::class, 'update']);
        Route::delete('/tasks/{id}', [ProjectTaskController::class, 'destroy']);
        Route::post('/projects/{projectId}/documents', [ProjectDocumentController::class, 'store']);
        Route::delete('/documents/{id}', [ProjectDocumentController::class, 'destroy']);
        Route::apiResource('projects', ProjectController::class);

        // Invoices
        Route::patch('/invoices/{id}/status', [InvoiceController::class, 'updateStatus']);
        Route::apiResource('invoices', InvoiceController::class);

        // Clients
        Route::apiResource('clients', ClientController::class);

        // Services
        Route::apiResource('services', ServiceController::class);

        // Portfolios
        Route::apiResource('portfolios', PortfolioController::class);

        // Blogs
        Route::apiResource('blogs', BlogController::class);

        // Salaries / Payroll
        Route::get('/salaries', [SalaryController::class, 'index']);
        Route::post('/salaries/pay', [SalaryController::class, 'pay']);

        // Finance Transactions
        Route::get('/finance/transactions', [FinanceController::class, 'index']);
        Route::post('/finance/transaction', [FinanceController::class, 'store']);

        // Users / Admin Management
        Route::post('/users/{id}/force-logout', [UserController::class, 'forceLogout']);
        Route::apiResource('users', UserController::class);

        // Roles & Permissions
        Route::get('/roles/permissions', [RoleController::class, 'permissions']);
        Route::apiResource('roles', RoleController::class);

        // Internal Documentations
        Route::apiResource('documentations', DocumentationController::class);

        // Audit Logs & Login Histories
        Route::get('/activity-logs', [AuditController::class, 'activityLogs']);
        Route::get('/login-histories', [AuditController::class, 'loginHistories']);
    });
});

// Backward Compatibility Top-Level Routes (/api/portfolios, etc)
Route::get('/portfolios', [PublicContentController::class, 'portfolios']);
Route::get('/services', [PublicContentController::class, 'services']);
Route::get('/blogs', [PublicContentController::class, 'blogs']);
Route::get('/clients', [PublicContentController::class, 'clients']);
Route::get('/documentations', [PublicContentController::class, 'documentations']);
