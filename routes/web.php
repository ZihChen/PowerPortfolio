<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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

Route::group(['middleware' => 'guest'], function() {

    Route::get('register', [AuthController::class, 'registerPage']);

    Route::post('register', [AuthController::class, 'register']);

    Route::get('login', [AuthController::class, 'loginPage']);

    Route::post('login', [AuthController::class, 'login']);

});

Route::group(['middleware' => 'auth'], function () {

    Route::get('dashboard', [DashboardController::class, 'getDashboard']);

    Route::get('user', [AuthController::class, 'getUser']);

    Route::get('logout', [AuthController::class, 'logout']);

});
