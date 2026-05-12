<?php

namespace App\Http\Requests\Transaction;

use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\IntegerType;

class GetTransactionsRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $StartDate,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $EndDate,

        #[Nullable, IntegerType]
        public ?int $CostCenterId,

        #[Nullable, StringType, In(['Income', 'Expense'])]
        public ?string $TransactionType,

        #[Nullable, IntegerType]
        public ?int $UserId,

        #[Nullable, BooleanType]
        public ?bool $IsActive,

        #[Nullable, IntegerType, Min(1)]
        public int $PageSize = 10,

        #[Nullable, IntegerType, Min(1)]
        public int $PageNumber = 1,
    ) {}
}
