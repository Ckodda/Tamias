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
use Spatie\LaravelData\Attributes\Validation\DateFormat;

class CreateEventRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $CostCenterId,

        #[Required, IntegerType]
        public int $CurrencyId,

        #[Required, StringType, Min(3)]
        public string $EventName,

        #[Required, Numeric, Min(0)]
        public float $TargetAmount,

        #[Required, StringType, In(['Active', 'Completed', 'Cancelled'])]
        public string $EventStatus,

        #[Required, DateFormat(format: 'Y-m-d')]
        public string $StartDate,
    ) {}
}
