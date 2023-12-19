<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\UrlController;
use App\Http\Controllers\Api\V2\UrlController as UrlControllerV2;

// Authentication
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ==========
// Version 1
// ==========
Route::prefix('v1')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/urls', [UrlController::class, 'index']);
        Route::post('/urls', [UrlController::class, 'store']);
        Route::get('{shortUrl}', [UrlController::class, 'redirectToOriginalUrl']);
    });
});

// ==========
// Version 2
// ==========
Route::prefix('v2')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/urls', [UrlControllerV2::class, 'index']);
        Route::post('/urls', [UrlControllerV2::class, 'store']);
        Route::get('{shortUrl}', [UrlControllerV2::class, 'redirectToOriginalUrl']);
    });
});
