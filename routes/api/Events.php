<?php

use App\Http\Controllers\Api\EventController;
use Illuminate\Support\Facades\Route;

Route::prefix('events')->middleware('auth:api')->group(function () {
    /**
     * GET /api/events
     * Obtiene el listado paginado de eventos.
     */
    Route::get('/', [EventController::class, 'index']);

    /**
     * POST /api/events
     * Registra un nuevo evento.
     */
    Route::post('/', [EventController::class, 'store']);

    /**
     * PUT /api/events
     * Actualiza un evento existente.
     */
    Route::put('/', [EventController::class, 'update']);
});
