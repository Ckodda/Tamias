<?php

namespace App\Models;

use App\Models\Traits\HasPascalCaseNaming;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @property int $Id
 * @property string $name
 * @property string $guard_name
 */
class Role extends SpatieRole
{
    use HasPascalCaseNaming;

    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'Roles';
}
