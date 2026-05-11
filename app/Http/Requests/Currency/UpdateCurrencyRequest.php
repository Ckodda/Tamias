<?php

namespace App\Http\Requests\Currency;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class UpdateCurrencyRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, StringType, Min(3), Max(50)]
        public ?string $CurrencyName,

        #[Nullable, StringType, Max(3)]
        public ?string $CurrencyCode,

        #[Nullable, StringType, Max(5)]
        public ?string $CurrencySymbol,

        #[Nullable, Numeric, Min(0)]
        public ?float $ExchangeRate,

        #[Nullable, BooleanType]
        public ?bool $IsActive,
    ) {}
}
