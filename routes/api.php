<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route for registering a new user
Route::post('/register', [AuthController::class, 'register']);

// Route for logging in to obtain a token
Route::post('/login', [AuthController::class, 'login']);

// Route for initiating password reset
Route::post('/initiate/password/reset', [AuthController::class, 'initiateResetPassword'])->name('password.email');
// Route for password reset
Route::post('/reset/password', [AuthController::class, 'resetPassword'])->name('password.update');
