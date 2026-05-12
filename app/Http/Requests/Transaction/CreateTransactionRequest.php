<?php

namespace App\Http\Requests\Transaction;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Numeric;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\StringType;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\IntegerType;
use Spatie\LaravelData\Attributes\Validation\Image;
use Spatie\LaravelData\Attributes\Validation\Max;
use Illuminate\Http\UploadedFile;

class CreateTransactionRequest extends Data
{
    public function __construct(
        #[Required, IntegerType]
        public int $UserId,

        #[Required, IntegerType]
        public int $CostCenterId,

        #[Required, IntegerType]
        public int $CurrencyId,

        #[Required, IntegerType]
        public int $PaymentMethodId,

        #[Required, Numeric, Min(0.01)]
        public float $TransactionAmount,

        #[Required, StringType, In(['Income', 'Expense'])]
        public string $TransactionType,

        #[Required, DateFormat('Y-m-d')]
        public string $AccountingPeriod,

        #[Required, StringType, Min(5)]
        public string $TransactionDescription,

        #[Nullable, IntegerType]
        public ?int $EventId,

        #[Nullable, IntegerType]
        public ?int $PendingExpenseId,

        #[Nullable, IntegerType]
        public ?int $LoanId,

        #[Nullable, Numeric, Min(0)]
        public float $AppliedExchangeRate = 1.0,

        #[Nullable, Image, Max(2048)]
        public ?UploadedFile $ReceiptImage,
    ) {}
}
