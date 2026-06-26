<?php

namespace App\Http\Requests\PendingExpense;

use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Data;

class GetPendingExpensesRequest extends Data
{
    public function __construct(
        #[Nullable, IntegerType]
        public ?int $Id,

        #[Nullable, IntegerType]
        public ?int $CostCenterId,

        #[Nullable, In(['Pending', 'Paid', 'Cancelled'])]
        public ?string $PaymentStatus,

        #[Nullable, StringType]
        public ?string $ProviderName,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $StartDate,

        #[Nullable, DateFormat('Y-m-d')]
        public ?string $EndDate,

        #[Nullable, IntegerType]
        public int $PageSize = 10,

        #[Nullable, IntegerType]
        public int $PageNumber = 1,
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
