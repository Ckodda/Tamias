<?php

namespace App\Http\Requests\PendingExpense;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdatePendingExpenseRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, IntegerType]
        public ?int $CostCenterId,

        #[Nullable, StringType, Min(5)]
        public ?string $ExpenseDescription,

        #[Nullable, Numeric, Min(0.01)]
        public ?float $TotalAmount,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $DueDate,

        #[Nullable, StringType, Min(3)]
        public ?string $ProviderName,

        #[Nullable, In(['Pending', 'Paid', 'Cancelled'])]
        public ?string $PaymentStatus,

        #[Nullable, BooleanType]
        public ?bool $IsActive,
    ) {}
}
