<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property Carbon $MonthPeriod
 * @property float $TotalIncomes
 * @property float $TotalExpenses
 * @property float $ClosingBalance
 * @property int $CostCenterId
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 *
 * -- Atributos Dinámicos (Procedimiento Almacenado)
 * @property string|null $CenterName
 * @property float|null $ProfitMarginPercentage
 * @property int|null $TotalCount
 */
#[Fillable([
    'MonthPeriod',
    'TotalIncomes',
    'TotalExpenses',
    'ClosingBalance',
    'CostCenterId',
    'IsActive',
    'CreatedBy',
    'UpdatedBy'
])]
class MonthlyBalance extends BaseModel
{
    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'MonthlyBalances';

    /**
     * Conversión de tipos.
     */
    protected function casts(): array
    {
        return [
            'MonthPeriod' => 'date',
            'TotalIncomes' => 'decimal:2',
            'TotalExpenses' => 'decimal:2',
            'ClosingBalance' => 'decimal:2',
            'IsActive' => 'boolean',
        ];
    }

}
