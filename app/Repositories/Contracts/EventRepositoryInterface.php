<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface
{
    /**
     * Crea un nuevo evento.
     */
    public function create(
        int $costCenterId,
        int $currencyId,
        string $eventName,
        float $targetAmount,
        string $eventStatus,
        string $startDate,
        ?int $createdBy = null
    ): Event;

    /**
     * Obtiene eventos con filtros y paginación.
     */
    public function getAll(
        ?int $id = null,
        ?string $eventName = null,
        ?int $currencyId = null,
        ?string $startDate = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    /**
     * Actualiza un evento existente.
     */
    public function update(
        int $id,
        ?int $costCenterId = null,
        ?int $currencyId = null,
        ?string $eventName = null,
        ?float $targetAmount = null,
        ?string $eventStatus = null,
        ?string $startDate = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): Event;
}
