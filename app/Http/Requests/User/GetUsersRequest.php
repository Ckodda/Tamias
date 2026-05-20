<?php

namespace App\Http\Requests\User;

use Spatie\LaravelData\Attributes\Validation\BooleanType;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class GetUsersRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int    $Id,
        #[Nullable, StringType]
        public ?string $FullName,
        #[Nullable, Email, Max(255)]
        public ?string $Email,
        #[Nullable, IntegerType]
        public ?int    $CreatedBy,
        #[Nullable, BooleanType]
        public ?bool   $IsActive,
        #[Nullable, IntegerType, Min(1)]
        public ?int    $PageSize = 10,

        #[Nullable, IntegerType, Min(1)]
        public ?int    $PageNumber = 1,
    )
    { }

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
