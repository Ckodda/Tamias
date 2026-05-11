<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property string $CodeCostCenter
 * @property string $CenterName
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable(['CodeCostCenter', 'CenterName', 'IsActive', 'CreatedBy', 'UpdatedBy'])]
class CostCenter extends BaseModel
{
    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'CostCenters';

    /**
     * Conversión de tipos.
     */
    protected function casts(): array
    {
        return [
            'IsActive' => 'boolean',
        ];
    }
}
