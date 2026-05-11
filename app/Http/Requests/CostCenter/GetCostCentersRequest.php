<?php

namespace App\Http\Requests\CostCenter;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\BooleanType;

class GetCostCentersRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,

        #[Nullable, StringType]
        public ?string $CenterName,

        #[Nullable, StringType]
        public ?string $CodeCostCenter,

        #[Nullable, BooleanType]
        public ?bool $IsActive,

        #[Nullable, IntegerType]
        public int $PageSize = 10,

        #[Nullable, IntegerType]
        public int $PageNumber = 1,
    ) {}
}
