<?php

namespace App\Repositories\Contracts;

use App\Models\CostCenter;
use Illuminate\Database\Eloquent\Collection;

interface CostCenterRepositoryInterface
{
    public function create(string $codeCostCenter, string $centerName, ?int $createdBy = null): CostCenter;

    public function getAll(
        ?int $id = null,
        ?string $centerName = null,
        ?string $codeCostCenter = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    /**
     * Actualiza un centro de costo existente (campos opcionales).
     */
    public function update(
        int $id,
        ?string $codeCostCenter = null,
        ?string $centerName = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): CostCenter;
}
