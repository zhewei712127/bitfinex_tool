<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BitfinexController;
use App\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot_password', [AuthController::class, 'forgotPassword']);
    Route::patch('/reset_password', [AuthController::class, 'resetPassword']);
    Route::group(['middleware' => 'auth.jwt'], function () {
        Route::get('/user_info', [AuthController::class, 'userInfo']);
        Route::get('/user', [AuthController::class, 'userList']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/update_user', [AuthController::class, 'updateUser']);
        Route::post('/update_password', [AuthController::class, 'updatePassword']);
    });
});

Route::group(['middleware' => 'auth.jwt'], function () {

    Route::get('/connection/{connection}', [ConnectionController::class, 'show'])->name('connection.show');
    Route::post('/connection', [ConnectionController::class, 'create'])->name('connection.create');

    Route::group(['prefix' => 'public'], function () {
        Route::get('/tickers', [BitfinexController::class, 'tickers'])->name('public.tickers');
        Route::get('/book', [BitfinexController::class, 'book'])->name('public.book');
    });

    Route::group(['prefix' => 'auth'], function () {
        Route::get('/wallets', [BitfinexController::class, 'wallets'])->name('auth.wallets');
        Route::get('/info', [BitfinexController::class, 'info'])->name('auth.info');
        Route::get('/funding_loads', [BitfinexController::class, 'fundingLoans'])->name('auth.fundingLoans');
        Route::get('/funding_loads_history', [BitfinexController::class, 'fundingLoansHistory'])->name('auth.fundingLoansHistory');
        Route::get('/orders_history', [BitfinexController::class, 'orders_history'])->name('auth.orders_history');
        Route::post('/order/create', [BitfinexController::class, 'orderCreate'])->name('auth.order.create');
        Route::post('/transfer', [BitfinexController::class, 'transfer'])->name('auth.transfer');
    });
});
