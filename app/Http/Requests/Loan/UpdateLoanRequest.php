<?php

namespace App\Http\Requests\Loan;

use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class UpdateLoanRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, StringType, Max(255)]
        public ?string $LenderName,

        #[Nullable, Numeric, Min(0)]
        public ?float $PrincipalAmount,

        #[Nullable, Numeric, Min(0)]
        public ?float $InterestAmount,

        #[Nullable, Numeric, Min(0)]
        public ?float $TotalToRepay,

        #[Nullable, Date('Y-m-d')]
        public ?string $RepaymentDueDate,

        #[Nullable, IntegerType]
        public ?int $CurrencyId,

        #[Nullable,StringType, In(['Pending', 'Paid'])]
        public ?string $LoanStatus = null,
    )
    { }
}
