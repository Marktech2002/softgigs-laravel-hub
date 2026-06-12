<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ListingController;
use Illuminate\Http\Request;

Route::prefix('v1')->group(function () {
    // Public Listing Routes
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{id}', [ListingController::class, 'show']);

    // Health Check
    Route::get('/health', function () {
        return \App\Libs\ApiResponse::success('API is healthy and running.');
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [UserController::class, 'register']);
        Route::post('/login', [UserController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [UserController::class, 'profile']);
            Route::post('/logout', [UserController::class, 'logout']);
            Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);
        });
    });

    // Protected Listing Routes
    Route::middleware(['auth:sanctum', \App\Middleware\AdminMiddleware::class])->group(function () {
        Route::post('/listings', [ListingController::class, 'store']);
        Route::put('/listings/{listing}', [ListingController::class, 'update']);
        Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');
});
