<?php

namespace App\Http\Requests\CostCenter;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\StringType;

class CreateCostCenterRequest extends Data
{
    public function __construct(
        #[Required, StringType, Min(3), Max(20)]
        public string $CodeCostCenter,

        #[Required, StringType, Min(3), Max(100)]
        public string $CenterName,
    ) {}
}
