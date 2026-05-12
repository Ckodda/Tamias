<?php

namespace App\Repositories\Contracts;

use App\Models\PendingExpense;
use Illuminate\Database\Eloquent\Collection;

interface PendingExpenseRepositoryInterface
{
    public function create(
        int $costCenterId,
        string $expenseDescription,
        float $totalAmount,
        string $dueDate,
        string $providerName,
        string $paymentStatus,
        ?int $createdBy = null
    ): PendingExpense;
    public function update(
        int $id,
        ?int $costCenterId = null,
        ?string $expenseDescription = null,
        ?float $totalAmount = null,
        ?string $dueDate = null,
        ?string $providerName = null,
        ?string $paymentStatus = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): PendingExpense;
    public function getAll(
        ?int $id = null,
        ?int $costCenterId = null,
        ?string $paymentStatus = null,
        ?string $providerName = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;
}
