<?php

use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->group(function () {
    // Rutas que estarán bajo /api/transactions
    Route::get('/', function () {
        return response()->json(['message' => 'Listado de Transacciones']);
    });

    Route::post('get-all', function () {
        // Aquí irá el llamado a tu procedure TransactionsPkg.GetTransactions
        return response()->json(['message' => 'Obtener todas las transacciones con filtros']);
    });

    // Agrega más rutas de transacciones aquí
});
