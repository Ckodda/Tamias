<?php

namespace App\Http\Requests\PaymentMethod;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class UpdatePaymentMethodRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, StringType, Min(3)]
        public ?string $MethodName,

        #[Nullable, BooleanType]
        public ?bool $IsActive,
    ) {}
}
