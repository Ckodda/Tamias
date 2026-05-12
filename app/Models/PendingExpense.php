<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property int $CostCenterId
 * @property string $ExpenseDescription
 * @property float $TotalAmount
 * @property Carbon $DueDate
 * @property string $ProviderName
 * @property string $PaymentStatus
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 *
 * @property-read CostCenter $CostCenter
 * @property-read User $Creator
 */
#[Fillable([
    'CostCenterId',
    'ExpenseDescription',
    'TotalAmount',
    'DueDate',
    'ProviderName',
    'PaymentStatus',
    'IsActive',
    'CreatedBy',
    'UpdatedBy'
])]
class PendingExpense extends BaseModel
{
    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'PendingExpenses';

    /**
     * Conversión de tipos de datos.
     */
    protected function casts(): array
    {
        return [
            'TotalAmount' => 'decimal:2',
            'DueDate' => 'date',
            'IsActive' => 'boolean',
            'CreatedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }
}
