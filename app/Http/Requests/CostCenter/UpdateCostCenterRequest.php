<?php

namespace App\Http\Requests\CostCenter;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;

class UpdateCostCenterRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $Id,

        #[Nullable, StringType]
        public ?string $CodeCostCenter,

        #[Nullable, StringType]
        public ?string $CenterName,

        #[Nullable, BooleanType]
        public ?bool $IsActive,
    ) {}
}
