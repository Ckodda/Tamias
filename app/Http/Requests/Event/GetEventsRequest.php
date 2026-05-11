<?php

namespace App\Http\Requests\Event;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Date;

class GetEventsRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,

        #[Nullable, StringType]
        public ?string $EventName,

        #[Nullable, IntegerType]
        public ?int $CurrencyId,

        #[Nullable, Date]
        public ?string $StartDate,

        #[Nullable, BooleanType]
        public ?bool $IsActive,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageSize = 10,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageNumber = 1,
    ) {}
}
