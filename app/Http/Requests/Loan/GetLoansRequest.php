<?php

namespace App\Http\Requests\Loan;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class GetLoansRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,
        #[Nullable, StringType]
        public ?string $LenderName,
        #[Nullable, IntegerType]
        public ?int $CurrencyId,
        #[Nullable, Date('Y-m-d')]
        public ?string $RepaymentDueDate,
        #[Nullable, BooleanType]
        public ?bool $IsActive,
        #[Nullable, StringType, In(['Pending', 'Paid'])]
        public ?string $LoanStatus,
        #[Nullable, IntegerType, Min(1)]
        public ?int $PageSize = 10,
        #[Nullable, IntegerType, Min(1)]
        public ?int $PageNumber = 1,
    )
    { }
}
