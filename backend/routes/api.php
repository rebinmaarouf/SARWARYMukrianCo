<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegistryController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

// Auth Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-2fa', [AuthController::class, 'verify2FA']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Admin Only
    Route::apiResource('users', UserController::class);
    Route::get('/roles', [UserController::class, 'roles']);
    Route::get('/permissions', [UserController::class, 'permissions']);
    
    // Accounting API resources
    Route::apiResource('registries', RegistryController::class);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('currencies', CurrencyController::class);
});
