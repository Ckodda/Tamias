<?php

namespace App\Models;

/**
 * Representa una fila del resultado del procedimiento GetUserCapabilities.
 * Al estar en el namespace Virtual, indicamos que no es una tabla física.
 */
class UserCapabilities
{
    public string $Type; // 'Role' | 'Permission'
    public string $Name; // Nombre del Rol o Permiso (ej: 'Admin' o 'Currency:Read')
}