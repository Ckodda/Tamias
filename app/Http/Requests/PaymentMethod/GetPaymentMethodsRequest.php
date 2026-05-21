<?php

namespace App\Http\Requests\PaymentMethod;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;

class GetPaymentMethodsRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,

        #[Nullable, StringType]
        public ?string $MethodName,

        #[Nullable, BooleanType]
        public ?bool $IsActive,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageSize = 10,

        #[Nullable, IntegerType, Min(1)]
        public ?int $PageNumber = 1,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('IsActive', $properties)) {
               if($properties['IsActive']==''){
                    $properties['IsActive'] = null;
               }
               else{
                    $properties['IsActive'] = filter_var($properties['IsActive'], FILTER_VALIDATE_BOOLEAN);
               }
        }

        return $properties;
    }
}
