<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntegrationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [IntegrationController::class, 'token']);

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    // Example protected route
    Route::get('/protected-data', function () {
        return response()->json([
            'success' => true,
            'data' => 'This is protected data accessible only with a valid access token'
        ]);
    });
});
