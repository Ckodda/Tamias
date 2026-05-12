<?php

namespace App\Http\Requests\Commitment;

use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class CreateCommitmentRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $UserId,
        #[Required, IntegerType]
        public int $CostCenterId,
        #[Required, IntegerType]
        public int $EventId,
        #[Required, Numeric, Min(0)]
        public float $CommitmentAmount,
        #[Required, StringType, In(['Monthly', 'OneTime'])]
        public string $FrequencyType,
        #[Required, StringType, In(['Active', 'Fulfilled', 'Cancelled'])]
        public string $CurrentStatus
    )
    { }
}
