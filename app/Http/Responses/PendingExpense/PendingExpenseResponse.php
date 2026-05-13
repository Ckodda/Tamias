<?php

namespace App\Http\Responses\PendingExpense;

use App\Models\PendingExpense;
use Illuminate\Support\Facades\Auth;

class PendingExpenseResponse
{
    public function __construct(
        public ?int $Id,
        public ?int $CostCenterId,
        public ?string $ExpenseDescription,
        public ?float $TotalAmount,
        public ?string $DueDate,
        public ?string $ProviderName,
        public ?string $PaymentStatus,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt,
    ) {}

    public static function fromModel(PendingExpense $model): self
    {
        $user = Auth::user();
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            CostCenterId: $model->CostCenterId,
            ExpenseDescription: $model->ExpenseDescription,
            TotalAmount: $model->TotalAmount,
            DueDate: $model->DueDate->toIso8601String(),
            ProviderName: $model->ProviderName,
            PaymentStatus: $model->PaymentStatus,
            IsActive: $model->IsActive,
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
