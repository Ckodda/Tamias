<?php

namespace App\Http\Responses\Loan;

use App\Models\Loan;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;

class LoanResponse extends Data
{
    public function __construct(
        public int $Id,
        public string $LenderName,
        public float $PrincipalAmount,
        public float $InterestAmount,
        public float $TotalToRepay,
        public string $RepaymentDueDate,
        public ?float $CurrentBalance,
        public string $LoanStatus,
        public bool $IsActive,
        public int $CurrencyId,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(Loan $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            LenderName: $model->LenderName,
            PrincipalAmount: $model->PrincipalAmount,
            InterestAmount: $model->InterestAmount,
            TotalToRepay: $model->TotalToRepay,
            RepaymentDueDate: $model->RepaymentDueDate->toIso8601String(),
            CurrentBalance: $model->CurrentBalance,
            LoanStatus: $model->LoanStatus,
            IsActive: $model->IsActive,
            CurrencyId: $model->CurrencyId,
            CreatedBy: $model->CreatedBy,
            UpdatedBy: $model->UpdatedBy,
            CreatedAt: $model->CreatedAt->toIso8601String(),
            UpdatedAt: $model->UpdatedAt->toIso8601String(),
        );

        if (!$canSeeAudit) {
            return $data->except('CreatedBy', 'UpdatedBy');
        }

        return $data;
    }
}
