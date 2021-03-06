<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FiscalOverviewController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserPositionController;
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

Route::group(['middleware' => ['auth', 'user.verify']], function () {

    Route::get('dashboard', [DashboardController::class, 'getDashboard']);

    Route::group(['prefix' => 'stocks'], function () {

        Route::get('search', [StockController::class, 'autocompleteSearch']);

        Route::post('/', [StockController::class, 'postUserStockRelation']);

        Route::post('/{stock_id}/position', [UserPositionController::class, 'postUserPosition']);

        Route::get('/{stock_id}/overview', [FiscalOverviewController::class, 'getStockFiscalOverview']);

        Route::delete('/{stock_id}/delete', [StockController::class, 'removeUserStockRelation']);

        Route::group(['prefix' => '{stock_id}/chart'], function () {

            Route::get('quote', [ChartController::class, 'getQuotesChart']);
        });
    });

    Route::get('user', [AuthController::class, 'getUser']);

    Route::get('logout', [AuthController::class, 'logout']);

});
