<?php

namespace App\Models;

/**
 * Representa una fila del resultado del procedimiento GetUserRolesAndPermissions.
 * Al estar en el namespace Virtual, indicamos que no es una tabla física.
 */
class UserRolesAndPermissions
{
    public string $Type; // 'Role' | 'Permission'
    public string $Name; // Nombre del Rol o Permiso (ej: 'Admin' o 'Currency:Read')
}