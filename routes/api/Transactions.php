<?php

use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {
    Route::get('/',[TransactionController::class,'index']);
    Route::post('/',[TransactionController::class,'store']);
    Route::post('/{id}/void',[TransactionController::class,'void']);
});
