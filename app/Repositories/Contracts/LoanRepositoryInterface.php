<?php

namespace App\Repositories\Contracts;

use App\Models\CostCenter;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;

interface LoanRepositoryInterface
{
    public function create(
        string $lenderName,
        float $principalAmount,
        float $interestAmount,
        float $totalToRepay,
        string $repaymentDueDate,
        string $loanStatus,
        bool $isActive,
        int $currencyId,
        int $createdBy,
        int $updatedBy): Loan;
    public function getAll(
        ?int $id = null,
        ?string $lenderName = null,
        ?int $currencyId = null,
        ?string $repaymentDueDate = null,
        ?bool $isActive = null,
        ?string $loanStatus = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection;

    public function update(
        int $id,
        ?string $lenderName,
        ?float $principalAmount,
        ?float $interestAmount,
        ?float $totalToRepay,
        ?string $repaymentDueDate,
        ?string $loanStatus,
        ?bool $isActive,
        ?int $currencyId,
        int $updatedBy
    ): Loan;
}
