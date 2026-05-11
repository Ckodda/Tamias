<?php

namespace App\Http\Requests\Currency;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class GetCurrenciesRequest extends Data
{
    public function __construct(
        #[Nullable, StringType]
        public ?string $CurrencyName,

        #[Nullable, StringType]
        public ?string $CurrencyCode,

        #[Nullable, BooleanType]
        public ?bool $IsActive,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageSize = 10,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageNumber = 1,
    ) {}
}
