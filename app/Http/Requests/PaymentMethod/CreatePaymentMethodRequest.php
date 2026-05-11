<?php

namespace App\Http\Requests\PaymentMethod;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;

class CreatePaymentMethodRequest extends Data
{
    public function __construct(
        #[Required, StringType, Min(3), Max(50)]
        public string $MethodName,
    ) {}
}
