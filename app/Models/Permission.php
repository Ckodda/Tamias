<?php

namespace App\Models;

use App\Models\Traits\HasPascalCaseNaming;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @property int $Id
 * @property string $name
 * @property string $guard_name
 */
class Permission extends SpatiePermission
{
    use HasPascalCaseNaming;

    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'Permissions';
}
