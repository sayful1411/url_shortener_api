<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

// ==========
// Version 1
// ==========
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        // auth
        Route::post('/logout', [AuthController::class, 'logout']);

    });
});
