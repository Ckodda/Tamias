<?php

namespace App\Repositories\Contracts;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Collection;

interface CurrencyRepositoryInterface
{
    /**
     * Crea una nueva moneda.
     */
    public function create(
        string $currencyName,
        string $currencyCode,
        string $currencySymbol,
        float $exchangeRate,
        ?int $createdBy = null
    ): Currency;

    /**
     * Obtiene todas las monedas con filtros y paginación.
     */
    public function getAll(
        ?string $currencyName = null,
        ?string $currencyCode = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    /**
     * Actualiza una moneda existente.
     */
    public function update(
        int $id,
        ?string $currencyName = null,
        ?string $currencyCode = null,
        ?string $currencySymbol = null,
        ?float $exchangeRate = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): Currency;
}
