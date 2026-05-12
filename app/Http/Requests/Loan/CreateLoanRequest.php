<?php

namespace App\Http\Requests\Loan;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Boolean;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;

class CreateLoanRequest extends Data
{
    public function __construct(
        #[Required, StringType, Max(255)]
        public string $LenderName,

        #[Required, Numeric, Min(0)]
        public float $PrincipalAmount,

        #[Required, Numeric, Min(0)]
        public float $InterestAmount,

        #[Required, Numeric, Min(0)]
        public float $TotalToRepay,

        #[Required, Date('Y-m-d')]
        public string $RepaymentDueDate, // date string

        #[Required, IntegerType]
        public int $CurrencyId,

        #[StringType, In(['Pending', 'Paid'])]
        public ?string $LoanStatus = null,
    ) {}
}
