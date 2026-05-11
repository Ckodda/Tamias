<?php

use App\Http\Controllers\Api\CostCenterController;
use Illuminate\Support\Facades\Route;

Route::prefix('cost-centers')->middleware('auth:api')->group(function () {

    Route::get('/', [CostCenterController::class, 'index']);
    Route::post('/', [CostCenterController::class, 'store']);
    Route::put('/', [CostCenterController::class, 'update']);
});
