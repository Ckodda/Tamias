<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function create(
        int $userId,
        int $costCenterId,
        int $currencyId,
        int $paymentMethodId,
        float $transactionAmount,
        string $transactionType,
        string $accountingPeriod,
        string $transactionDescription,
        int $createdBy,
        ?int $eventId = null,
        ?int $pendingExpenseId = null,
        ?int $loanId = null,
        float $appliedExchangeRate = 1.0,
        ?string $receiptImagePath = null
    ): Transaction;

    public function void(int $id, int $updatedBy): bool;
    public function getAll(
        ?int $id = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $costCenterId = null,
        ?string $transactionType = null,
        ?int $userId = null,
        ?bool $isActive = null
    ): Collection;
}
