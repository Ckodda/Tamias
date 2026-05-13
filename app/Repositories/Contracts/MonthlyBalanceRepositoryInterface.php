<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface MonthlyBalanceRepositoryInterface
{
    public function getAll(
        ?int $costCenterId,
        ?string $startMonth,
        ?string $endMonth,
        int $pageSize,
        int $pageNumber
    ): Collection;
}
