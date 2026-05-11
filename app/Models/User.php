<?php

namespace App\Models;

use App\Models\Traits\HasPascalCaseNaming;
use App\Models\Traits\HasAuditColumns;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $Id
 * @property string $FullName
 * @property string $Email
 * @property string $Password
 * @property bool $IsActive
 * @property Carbon $CreatedAt
 * @property Carbon $UpdatedAt
 * @property int|null $CreatedBy
 * @property int|null $UpdatedBy
 *
 * @mixin Builder
 */
#[Fillable(['FullName', 'Email', 'Password', 'IsActive', 'CreatedBy', 'UpdatedBy'])]
#[Hidden(['Password'])]
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasPascalCaseNaming, HasRoles, HasAuditColumns;

    /**
     * El nombre de la tabla asociada al modelo.
     */
    protected $table = 'Users';

    /**
     * El identificador de autenticación debe ser la llave primaria 'Id'.
     */
    public function getAuthIdentifierName(): string
    {
        return 'Id';
    }

    /**
     * Indica a Laravel que la columna de la contraseña es Password.
     */
    public function getAuthPasswordName(): string
    {
        return 'Password';
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Mutador para asegurar que la contraseña siempre se guarde encriptada.
     */
    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
            'CreatedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }
}
