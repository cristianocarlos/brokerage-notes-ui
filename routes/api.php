<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntegrationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [IntegrationController::class, 'token']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/access-token-refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', [AuthController::class, 'user']);
    // Example protected route
    Route::get('/protected-data', function () {
        return response()->json([
            'success' => true,
            'data' => 'This is protected data accessible only with a valid access token'
        ]);
    });
});
