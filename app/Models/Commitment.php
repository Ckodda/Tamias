<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property int $UserId
 * @property int $CostCenterId
 * @property int|null $EventId
 * @property float $CommitmentAmount
 * @property string $FrequencyType
 * @property string $CurrentStatus
 * @property boolean $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable([
    'UserId',
    'CostCenterId',
    'EventId',
    'CommitmentAmount',
    'FrequencyType',
    'CurrentStatus',
    'IsActive',
    'CreatedBy',
    'UpdatedBy'
])]
class Commitment extends BaseModel
{

    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'Commitments';

    /**
     * Conversión de tipos.
     */
    protected function casts(): array
    {
        return [
            'CommitmentAmount' => 'decimal:2',
            'CreatedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }
}
