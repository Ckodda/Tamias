<?php

namespace App\Http\Responses\Transaction;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelData\Data;

class TransactionResponse extends Data
{
    public function __construct(
        public int $Id,
        public ?int $UserId,
        public int $CostCenterId,
        public ?int $EventId,
        public ?int $PendingExpenseId,
        public ?int $LoanId,
        public int $CurrencyId,
        public int $PaymentMethodId,
        public float $TransactionAmount,
        public string $TransactionType,
        public float $AppliedExchangeRate,
        public string $AccountingPeriod,
        public string $TransactionDescription,
        public ?string $ReceiptImagePath,
        public bool $IsActive,
        public ?int $CreatedBy,
        public ?int $UpdatedBy,
        public string $CreatedAt,
        public string $UpdatedAt
    )
    { }

    public static function fromModel(Transaction $model): self
    {
        $user = Auth::user();
        // Definimos si el usuario tiene permisos para ver auditoría
        $canSeeAudit = $user && ($user->hasRole('SuperAdmin') || $user->hasRole('Admin'));

        $data = new self(
            Id: $model->Id,
            UserId: $model->UserId,
            CostCenterId: $model->CostCenterId,
            EventId: $model->EventId,
            PendingExpenseId: $model->PendingExpenseId,
            LoanId: $model->LoanId,
            CurrencyId: $model->CurrencyId,
            PaymentMethodId: $model->PaymentMethodId,
            TransactionAmount: (float) $model->TransactionAmount,
            TransactionType: $model->TransactionType,
            AppliedExchangeRate: (float) $model->AppliedExchangeRate,
            AccountingPeriod: $model->AccountingPeriod->format('Y-m-d'),
            TransactionDescription: $model->TransactionDescription,
            ReceiptImagePath: $model->ReceiptImagePath,
            IsActive: $model->IsActive,
            CreatedBy: $model->CreatedBy,
            UpdatedBy: $model->UpdatedBy,
            CreatedAt: $model->CreatedAt->toIso8601String(),
            UpdatedAt: $model->UpdatedAt->toIso8601String()
        );

        // Si el usuario no es Admin/SuperAdmin, se excluyen los campos sensibles del JSON
        if (!$canSeeAudit) {
            return $data->except('CreatedBy', 'UpdatedBy');
        }

        return $data;
    }
}
