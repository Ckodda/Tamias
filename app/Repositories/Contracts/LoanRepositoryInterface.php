<?php

namespace App\Repositories\Contracts;

use App\Models\CostCenter;
use App\Models\Loan;

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
}
