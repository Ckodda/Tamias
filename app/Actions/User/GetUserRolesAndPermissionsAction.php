<?php

namespace App\Actions\User;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Http\Responses\User\UserRolesAndPermissionsResponse;
use Illuminate\Support\Collection;

class GetUserRolesAndPermissionsAction
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la obtención de roles y permisos del usuario.
     *
     * @param int $userId
     * @return Collection<int, UserRolesAndPermissionsResponse>
     */
    public function execute(int $userId): Collection
    {
        $collection = $this->repository->getRolesAndPermissions($userId);

        $items = $collection->map(fn($model) => UserRolesAndPermissionsResponse::fromModel($model));
        return $items;
    }
}