<?php

use App\Http\Controllers\Api\CommitmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('commitments')->middleware('auth:api')->group(function () {

    Route::get('/', [CommitmentController::class, 'index']);
    Route::post('/', [CommitmentController::class, 'store']);
    Route::put('/', [CommitmentController::class, 'update']);
});
