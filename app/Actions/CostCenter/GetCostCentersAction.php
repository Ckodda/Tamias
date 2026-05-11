<?php

namespace App\Actions\CostCenter;

use App\Http\Requests\CostCenter\GetCostCentersRequest;
use App\Http\Responses\CostCenter\CostCenterResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\CostCenterRepositoryInterface;

class GetCostCentersAction
{
    public function __construct(
        protected CostCenterRepositoryInterface $repository
    ) {}

    /**
     * Obtiene centros de costo paginados.
     *
     * @param GetCostCentersRequest $request
     * @return PaginatedResponse
     */
    public function execute(GetCostCentersRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            centerName: $request->CenterName,
            codeCostCenter: $request->CodeCostCenter,
            isActive: $request->IsActive,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($model) => CostCenterResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
