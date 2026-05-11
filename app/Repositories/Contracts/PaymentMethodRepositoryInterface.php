<?php

namespace App\Repositories\Contracts;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

interface PaymentMethodRepositoryInterface
{
    public function create(string $methodName, ?int $createdBy = null): PaymentMethod;

    public function getAll(
        ?int $id = null,
        ?string $methodName = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    public function update(
        int $id,
        ?string $methodName = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): PaymentMethod;
}
