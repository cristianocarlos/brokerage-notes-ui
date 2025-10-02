<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TokenAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () { return inertia('Home'); })
    ->middleware('auth');

Route::view('/', 'welcome');
Route::view('/login', 'login')
    ->name('login');
Route::post('/login', LoginController::class)
    ->middleware('throttle:5,1')
    ->name('login.attempt');
Route::any('/logout', [LoginController::class, 'logout'])
    ->name('logout');
Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');
Route::view('/register', 'register')
    ->name('register');
Route::post('/register', RegisterController::class)
    ->name('register.store');

//

Route::get('/token-login', [TokenAuthController::class, 'tokenLogin']);
