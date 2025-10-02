<?php

use App\Http\Controllers\IntegrationController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [IntegrationController::class, 'token']);
