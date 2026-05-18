<?php

namespace App\Http\Responses\User;

use Spatie\LaravelData\Data;
use Illuminate\Support\Collection;

class UserCapabilitiesResponse extends Data
{
    public function __construct(
        /** @var string[] Listado de nombres de roles */
        public array $Roles,
        /** @var array<string, string[]> Permisos agrupados por Módulo => [Acciones] */
        public array $Permissions
    ) {}

    /**
     * Mapea la colección de objetos virtuales devuelta por el repositorio
     * hacia una estructura de "Mapa de Capacidades".
     * 
     * @param Collection $collection Colección de objetos UserCapabilities
     * @return self
     */
    public static function fromCollection(Collection $collection): self
    {
        // Extraemos los Roles como una lista plana de strings
        $roles = $collection->where('Type', 'Role')
            ->pluck('Name')
            ->values()
            ->toArray();

        // Agrupamos los permisos por módulo separando el string 'Modulo:Accion'
        $permissionsGrouped = [];
        $permissionsRaw = $collection->where('Type', 'Permission')->pluck('Name');

        foreach ($permissionsRaw as $permission) {
            if (str_contains($permission, ':')) {
                [$module, $action] = explode(':', $permission, 2);
                $permissionsGrouped[$module][] = $action;
            } else {
                $permissionsGrouped['General'][] = $permission;
            }
        }

        return new self(
            Roles: $roles,
            Permissions: $permissionsGrouped
        );
    }
}