<?php

namespace App\Repositories\Contracts;

use App\Models\Virtual\UserCapabilityResult;
use App\Models\User;
use App\Models\UserRolesAndPermissions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface UserRepositoryInterface
{
    public function create(string $fullName, string $email, string $password, int $createdBy): User;

    public function getAll(
        ?int    $id = null,
        ?string $fullName = null,
        ?string $email = null,
        ?bool   $isActive = null,
        int     $pageSize = 10,
        int     $pageNumber = 1
    ): Collection;

    public function update(
        int     $id,
        ?string $fullName = null,
        ?string $email = null,
        ?string $password = null,
        ?bool   $isActive = null,
        ?int    $updatedBy = null
    ): User;

    /**
     * Obtiene los roles y permisos de un usuario.
     * 
     * @param int $userId
     * @return SupportCollection<int, UserRolesAndPermissions>
     */
    public function getRolesAndPermissions(int $userId): SupportCollection;
}
