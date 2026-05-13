<?php

namespace App\Http\Requests\PendingExpense;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreatePendingExpenseRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $CostCenterId,

        #[Required, StringType, Min(5)]
        public string $ExpenseDescription,

        #[Required, Numeric, Min(0.01)]
        public float $TotalAmount,

        #[Required, DateFormat('Y-m-d')]
        public string $DueDate,

        #[Required, StringType, Min(3)]
        public string $ProviderName,

        #[Required, In(['Pending', 'Paid', 'Cancelled'])]
        public string $PaymentStatus,
    ) {}
}
