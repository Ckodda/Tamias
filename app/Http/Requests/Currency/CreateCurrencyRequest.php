<?php

namespace App\Http\Requests\Currency;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\StringType;

class CreateCurrencyRequest extends Data
{
    public function __construct(
        #[Required, StringType, Min(3), Max(50)]
        public string $CurrencyName,

        #[Required, StringType, Max(3)]
        public string $CurrencyCode,

        #[Required, StringType, Max(5)]
        public string $CurrencySymbol,

        #[Required, Numeric, Min(0)]
        public float $ExchangeRate,
    ) {}
}
