<?php

namespace App\Http\Requests\Commitment;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class GetCommitmentsRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,
        #[Nullable, IntegerType]
        public ?int $UserId,
        #[Nullable, IntegerType]
        public ?int $CostCenterId,
        #[Nullable, IntegerType]
        public ?int $EventId,
        #[Nullable, StringType, In(['Active', 'Fulfilled', 'Cancelled'])]
        public ?string $CurrentStatus,
        #[Nullable, IntegerType]
        public int $PageSize = 10,
        #[Nullable, IntegerType]
        public int $PageNumber = 1,
    ) {}
}
