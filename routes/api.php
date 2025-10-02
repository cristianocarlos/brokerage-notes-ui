<?php

use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\TokenAuthController;
use App\Http\Controllers\TokenDataController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [IntegrationController::class, 'token']);

//////////////////
//////////////////

Route::post('/jwt-register', [TokenAuthController::class, 'register']);
Route::post('/jwt-login', [TokenAuthController::class, 'login']);

// Protected routes
Route::middleware('auth:api')->group(function () {
    // Auth routes
    Route::post('/jwt-logout', [TokenAuthController::class, 'logout']);
    Route::post('/jwt-refresh', [TokenAuthController::class, 'refresh']);
    Route::get('/jwt-user', [TokenAuthController::class, 'user']);
    // Data routes
    Route::get('/jwt-protected-data', [TokenDataController::class, 'protectedData']);
});
Route::get('/jwt-user-data', [TokenDataController::class, 'userData']);
