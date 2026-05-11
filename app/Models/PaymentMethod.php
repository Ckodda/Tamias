<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Carbon;

/**
 * @property int $Id
 * @property string $MethodName
 * @property bool $IsActive
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 */
#[Fillable(['MethodName', 'IsActive', 'CreatedBy', 'UpdatedBy'])]
class PaymentMethod extends BaseModel
{
    protected $table = 'PaymentMethods';

    protected function casts(): array
    {
        return [
            'IsActive' => 'boolean',
        ];
    }
}
