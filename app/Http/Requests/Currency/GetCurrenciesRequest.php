<?php

namespace App\Http\Requests\Currency;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithCastable;

class GetCurrenciesRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,
        
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
