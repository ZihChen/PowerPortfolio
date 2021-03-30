<?php

use App\Http\Controllers\AuthController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', [\App\Http\Controllers\StockController::class, 'test']);

Route::post('sign_up', [AuthController::class, 'signUp']);

Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('user', [AuthController::class, 'getUser']);

    Route::get('logout', [AuthController::class, 'logout']);

});
