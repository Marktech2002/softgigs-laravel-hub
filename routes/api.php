<?php

use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Libs\ApiResponse;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Health Check
    |--------------------------------------------------------------------------
    */
    Route::get('/health', fn () => ApiResponse::success('API is healthy and running.'));

    /*
    |--------------------------------------------------------------------------
    | Public Routes
    |--------------------------------------------------------------------------
    */
    Route::controller(ListingController::class)->group(function () {
        Route::get('/listings', 'index');
        Route::get('/listings/{id}', 'show');
    });

    Route::prefix('auth')->controller(UserController::class)->group(function () {
        Route::post('/register', 'register');
        Route::post('/login', 'login');
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Requires Sanctum Token)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        // Auth & Bookmarks
        Route::prefix('auth')->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/profile', 'profile');
                Route::post('/logout', 'logout');
                Route::post('/user/avatar', 'uploadAvatar');
            });

            Route::controller(BookmarkController::class)->group(function () {
                Route::get('/bookmarks', 'index');
                Route::post('/listings/{listing}/bookmark', 'toggle');
            });
        });

        // Admin Listing Management
        Route::middleware(\App\Middleware\AdminMiddleware::class)
            ->controller(ListingController::class)
            ->group(function () {
                Route::post('/listings', 'store');
                Route::put('/listings/{listing}', 'update');
                Route::delete('/listings/{listing}', 'destroy');
            });

        // Current User Fallback
        Route::get('/user', fn (Request $request) => $request->user());
    });
});
