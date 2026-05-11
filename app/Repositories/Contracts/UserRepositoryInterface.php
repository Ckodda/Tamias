<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

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
}
