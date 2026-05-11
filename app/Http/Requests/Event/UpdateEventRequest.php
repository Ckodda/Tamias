<?php

namespace App\Http\Requests\Event;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class UpdateEventRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, IntegerType]
        public ?int $CostCenterId,

        #[Nullable, IntegerType]
        public ?int $CurrencyId,

        #[Nullable, StringType, Min(3)]
        public ?string $EventName,

        #[Nullable, Numeric, Min(0)]
        public ?float $TargetAmount,

        #[Nullable, StringType, In(['Active', 'Completed', 'Cancelled'])]
        public ?string $EventStatus,

        #[Nullable, Date]
        public ?string $StartDate,

        #[Nullable, BooleanType]
        public ?bool $IsActive,
    ) {}
}
