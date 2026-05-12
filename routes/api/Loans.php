<?php
use App\Http\Controllers\Api\LoanController;
use Illuminate\Support\Facades\Route;

Route::prefix('loans')->middleware('auth:api')->group(function () {

    Route::get('/', [LoanController::class, 'index']);

    Route::post('/', [LoanController::class, 'store']);

    Route::put('/', [LoanController::class, 'update']);
});
