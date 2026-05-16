<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;

// Namespaces
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Finance\AccountController;
use App\Http\Controllers\Api\Finance\CurrencyController;
use App\Http\Controllers\Api\Finance\TransferController;
use App\Http\Controllers\Api\Finance\ExchangeController;
use App\Http\Controllers\Api\Finance\VoucherController;
use App\Http\Controllers\Api\Finance\RegistryController;
use App\Http\Controllers\Api\Finance\AuditReportController;
use App\Http\Controllers\Api\Finance\JournalController;
use App\Http\Controllers\Api\Finance\ActivityLogController;

// Public/Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin Domain
    Route::prefix('admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::get('/all-permissions', [RoleController::class, 'getAllPermissions']);
    });

    // Dashboard Stats
    Route::get('dashboard/stats', [DashboardController::class, 'getStats']);

    // Finance Domain
    Route::apiResource('registries', RegistryController::class);
    Route::get('audit-advanced', [AuditReportController::class, 'getAdvancedAudit']);
    Route::get('audit-advanced/verify', [AuditReportController::class, 'verifyIntegrity']);
    Route::get('smart-analytics', [\App\Http\Controllers\Api\Finance\SmartAnalyticsController::class, 'index']);
    Route::get('accounts/recalculate', [AccountController::class, 'recalculateBalances']);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('transfers', TransferController::class);
    Route::post('currencies/update-rate', [CurrencyController::class, 'updateRate']);
    Route::apiResource('currencies', CurrencyController::class);
    Route::apiResource('exchanges', ExchangeController::class);
    Route::apiResource('vouchers', VoucherController::class);
    Route::get('journals', [JournalController::class, 'index']);
    Route::get('journals/{id}', [JournalController::class, 'show']);
    Route::delete('journals/{id}', [JournalController::class, 'destroy']);
    
    // Reports & Audit
    Route::middleware('can:view_forensics')->group(function () {
        Route::get('audit-logs', [ActivityLogController::class, 'index']);
        Route::get('audit-logs/{id}', [ActivityLogController::class, 'show']);
    });
    Route::get('reports/profit', [ExchangeController::class, 'getProfitReport']);
    Route::get('reports/unified', [App\Http\Controllers\Api\Finance\FinancialReportController::class, 'getUnifiedReport']);
    Route::get('/dashboard/aging-debts', [\App\Http\Controllers\Api\Finance\AgingDebtController::class, 'index']);
    Route::apiResource('vouchers', \App\Http\Controllers\Api\Finance\VoucherController::class)->except(['update']);
    
    // Multi-Branch Support
    Route::get('branches', [BranchController::class, 'index']);
    Route::post('branches/switch', [BranchController::class, 'switch']);
});
