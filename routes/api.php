<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\UrlController;

// ==========
// Version 1
// ==========
Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        // auth
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/urls', [UrlController::class, 'index']);
        Route::post('/urls', [UrlController::class, 'store']);
        Route::get('{shortUrl}', [UrlController::class, 'redirectToOriginalUrl']);
    });
});
