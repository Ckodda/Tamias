<?php

namespace App\Actions\MonthlyBalance;

use App\Http\Requests\CostCenter\GetCostCentersRequest;
use App\Http\Requests\MonthlyBalance\GetMonthlyBalancesRequest;
use App\Http\Responses\CostCenter\CostCenterResponse;
use App\Http\Responses\MonthlyBalance\MonthlyBalanceResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\CostCenterRepositoryInterface;
use App\Repositories\Eloquent\MonthlyBalanceRepository;

class GetMonthlyBalancesAction
{
    public function __construct(
        protected MonthlyBalanceRepository $repository
    ) {}

    /**
     * Obtiene MonthlyBalances paginados.
     *
     * @param GetMonthlyBalancesRequest $request
     * @return PaginatedResponse
     * @throws \Exception
     */
    public function execute(GetMonthlyBalancesRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            costCenterId: $request->CostCenterId,
            startMonth: $request->StartMonth,
            endMonth: $request->EndMonth,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($model) => MonthlyBalanceResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
