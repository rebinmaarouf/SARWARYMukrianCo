<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Namespaces
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Finance\AccountController;
use App\Http\Controllers\Api\Finance\CurrencyController;
use App\Http\Controllers\Api\Finance\ExchangeController;
use App\Http\Controllers\Api\Finance\RegistryController;

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
        Route::get('/roles', [UserController::class, 'roles']);
        Route::get('/permissions', [UserController::class, 'permissions']);
        Route::get('/dashboard/stats', [DashboardController::class, 'stats']); // Fixed to match controller
    });

    // Dashboard Stats (Alternative global path)
    Route::get('dashboard/stats', [DashboardController::class, 'getStats']);

    // Finance Domain
    Route::apiResource('registries', RegistryController::class);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('currencies', CurrencyController::class);
    Route::apiResource('exchanges', ExchangeController::class);
    Route::get('reports/profit', [ExchangeController::class, 'getProfitReport']);
});
