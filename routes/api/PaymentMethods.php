<?php

use App\Http\Controllers\Api\PaymentMethodController;
use Illuminate\Support\Facades\Route;

Route::prefix('payment-methods')->middleware('auth:api')->group(function () {
    Route::get('/', [PaymentMethodController::class, 'index']);
    Route::post('/', [PaymentMethodController::class, 'store']);
    Route::put('/', [PaymentMethodController::class, 'update']);
});
