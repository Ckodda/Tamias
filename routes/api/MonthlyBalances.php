<?php

use App\Http\Controllers\Api\CostCenterController;
use App\Http\Controllers\Api\MonthlyBalanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('monthly-balances')->middleware('auth:api')->group(function () {

    Route::get('/', [MonthlyBalanceController::class, 'index']);
    Route::post('/', [MonthlyBalanceController::class, 'store']);
    Route::put('/', [MonthlyBalanceController::class, 'update']);
});
