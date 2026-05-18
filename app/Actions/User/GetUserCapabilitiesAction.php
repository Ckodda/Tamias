<?php

namespace App\Actions\User;

use App\Http\Responses\User\UserCapabilitiesResponse;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;

class GetUserCapabilitiesAction
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la obtención de roles y permisos del usuario.
     *
     * @param int $userId
     * @return UserCapabilitiesResponse Mapa de capacidades del usuario, incluyendo roles y permisos agrupados por módulo.
     */
    public function execute(int $userId): UserCapabilitiesResponse
    {
        $collection = $this->repository->getRolesAndPermissions($userId);

        
        return UserCapabilitiesResponse::fromCollection($collection);
    }
}