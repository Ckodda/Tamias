<?php

namespace App\Http\Requests\MonthlyBalance;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\IntegerType;

class GetMonthlyBalancesRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $CostCenterId,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $StartMonth,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $EndMonth,

        #[Nullable, IntegerType]
        public int $PageSize = 12,

        #[Nullable, IntegerType]
        public int $PageNumber = 1,
    ) {}
}
