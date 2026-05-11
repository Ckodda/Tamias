<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property string $CurrencyName
 * @property string $CurrencyCode
 * @property string $CurrencySymbol
 * @property float $ExchangeRate
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable(['CurrencyName', 'CurrencyCode', 'CurrencySymbol', 'ExchangeRate', 'IsActive', 'CreatedBy', 'UpdatedBy'])]
class Currency extends BaseModel
{
    protected $table = 'Currencies';

    protected function casts(): array
    {
        return [
            'ExchangeRate' => 'float',
            'IsActive' => 'boolean',
        ];
    }
}
