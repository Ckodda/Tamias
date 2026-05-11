<?php

use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::prefix('currencies')->middleware('auth:api')->group(function () {
    /**
     * GET /api/currencies
     * Obtiene el listado paginado de monedas.
     */
    Route::get('/', [CurrencyController::class, 'index']);

    /**
     * POST /api/currencies
     * Registra una nueva moneda.
     */
    Route::post('/', [CurrencyController::class, 'store']);

    /**
     * PUT /api/currencies
     * Actualiza una moneda existente.
     */
    Route::put('/', [CurrencyController::class, 'update']);
});
