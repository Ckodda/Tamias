<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property string $LenderName
 * @property float $PrincipalAmount
 * @property float $InterestAmount
 * @property float $TotalToRepay
 * @property Carbon $RepaymentDueDate
 * @property float $CurrentBalance
 * @property string $LoanStatus
 * @property bool $IsActive
 * @property int $CurrencyId
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable([
    'LenderName',
    'PrincipalAmount',
    'InterestAmount',
    'TotalToRepay',
    'RepaymentDueDate',
    'CurrentBalance',
    'LoanStatus',
    'IsActive',
    'CurrencyId',
    'CreatedBy',
    'UpdatedBy'
])]
class Loan extends BaseModel
{
    protected $table = 'Loans';

    protected function casts(): array
    {
        return [
            'PrincipalAmount' => 'float',
            'InterestAmount' => 'float',
            'TotalToRepay' => 'float',
            'RepaymentDueDate' => 'date',
            'IsActive' => 'boolean',
            'CurrencyId' => 'integer',
            'CreatedBy' => 'integer',
            'UpdatedBy' => 'integer',
        ];
    }
}
