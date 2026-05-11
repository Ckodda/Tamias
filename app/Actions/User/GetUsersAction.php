<?php

namespace App\Actions\User;

use App\Http\Requests\User\GetUsersRequest;
use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\User\UserResponse;
use App\Repositories\Contracts\UserRepositoryInterface;

class GetUsersAction
{
    public function __construct(
        protected UserRepositoryInterface $repository
    ) {}

    public function execute(GetUsersRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            fullName: $request->FullName,
            email: $request->Email,
            isActive: $request->IsActive,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        // Extraemos el TotalCount del primer elemento de la colección de modelos
        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos a Response objects
        $items = $collection->map(fn($model) => UserResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
