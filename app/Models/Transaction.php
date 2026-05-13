<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property int|null $UserId
 * @property int $CostCenterId
 * @property int|null $EventId
 * @property int|null $PendingExpenseId
 * @property int|null $LoanId
 * @property int $CurrencyId
 * @property int $PaymentMethodId
 * @property float $TransactionAmount
 * @property string $TransactionType
 * @property float $AppliedExchangeRate
 * @property Carbon $AccountingPeriod
 * @property string $TransactionDescription
 * @property string|null $ReceiptImagePath
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 *
 * @property-read User|null $User
 * @property-read CostCenter $CostCenter
 * @property-read Currency $Currency
 * @property-read PaymentMethod $PaymentMethod
 * @property-read Loan|null $Loan
 * @property-read PendingExpense|null $PendingExpense
 *
 * @property string|null $UserFullName
 * @property string|null $CostCenterName
 * @property string|null $CurrencySymbol
 * @property string|null $PaymentMethodName
 */
#[Fillable([
    'UserId',
    'CostCenterId',
    'EventId',
    'PendingExpenseId',
    'LoanId',
    'CurrencyId',
    'PaymentMethodId',
    'TransactionAmount',
    'TransactionType',
    'AppliedExchangeRate',
    'AccountingPeriod',
    'TransactionDescription',
    'ReceiptImagePath',
    'IsActive',
    'CreatedBy',
    'UpdatedBy'
])]
class Transaction extends BaseModel
{
    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'Transactions';

    /**
     * Conversión de tipos de datos.
     */
    protected function casts(): array
    {
        return [
            'TransactionAmount' => 'decimal:2',
            'AppliedExchangeRate' => 'decimal:4',
            'AccountingPeriod' => 'date',
            'IsActive' => 'boolean',
            'CreatedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }

}
