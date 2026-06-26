<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property int $CostCenterId
 * @property string $CostCenterName
 * @property int $CurrencyId
 * @property string $CurrencyCode
 * @property string $CurrencySymbol
 * @property string $CurrencyName
 * @property string $EventName
 * @property float $TargetAmount
 * @property string $EventStatus
 * @property Carbon $StartDate
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable(['CostCenterId', 'EventName', 'TargetAmount', 'EventStatus', 'StartDate', 'IsActive', 'CreatedBy', 'UpdatedBy'])]
class Event extends BaseModel
{
    protected $table = 'Events';

    protected function casts(): array
    {
        return [
            'TargetAmount' => 'float',
            'IsActive' => 'boolean',
            'StartDate' => 'date',
        ];
    }
}
