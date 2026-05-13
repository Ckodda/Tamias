<?php

use App\Http\Controllers\Api\CostCenterController;
use App\Http\Controllers\Api\PendingExpenseController;
use Illuminate\Support\Facades\Route;

Route::prefix('pending-expenses')->middleware('auth:api')->group(function () {

    Route::get('/', [PendingExpenseController::class, 'index']);
    Route::post('/', [PendingExpenseController::class, 'store']);
    Route::put('/', [PendingExpenseController::class, 'update']);
});
