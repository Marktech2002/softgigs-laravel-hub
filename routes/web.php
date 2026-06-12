<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Libs\ApiResponse;

Route::get('/', function () {
    return ApiResponse::success('welcome to soft gigs Tawab hub for Laravel projects');
});
