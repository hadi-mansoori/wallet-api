<?php

use App\Http\Controllers\Api\V1\WalletController;
use App\Http\Controllers\Api\V1\DepositController;
use App\Http\Controllers\Api\V1\WithdrawalController;
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

Route::group(['prefix' => 'v1'], function () {

    Route::get('wallet-deposits/trash', [DepositController::class, 'trash']);
    Route::put('wallet-deposits/restore/{id}', [DepositController::class, 'restore']);
    Route::apiResource('wallet-deposits', DepositController::class);

    Route::get('wallet-withdrawals/trash', [WithdrawalController::class, 'trash']);
    Route::put('wallet-withdrawals/restore/{id}', [WithdrawalController::class, 'restore']);
    Route::apiResource('wallet-withdrawals', WithdrawalController::class);

    Route::post('wallets/balance', [WalletController::class, 'balance']);
    Route::post('wallets/deposit', [WalletController::class, 'deposits']);
    Route::post('wallets/withdrawal', [WalletController::class, 'withdrawals']);

});

