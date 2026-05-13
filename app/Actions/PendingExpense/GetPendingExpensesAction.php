<?php

namespace App\Actions\PendingExpense;

use App\Http\Requests\PendingExpense\GetPendingExpensesRequest;
use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\PendingExpense\PendingExpenseResponse;
use App\Repositories\Contracts\PendingExpenseRepositoryInterface;

class GetPendingExpensesAction
{
    public function __construct(
        protected PendingExpenseRepositoryInterface $repository
    ) {}

    public function execute(GetPendingExpensesRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            costCenterId: $request->CostCenterId,
            paymentStatus: $request->PaymentStatus,
            providerName: $request->ProviderName,
            startDate: $request->StartDate,
            endDate: $request->EndDate,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber,
        );

        // Extraemos el TotalCount del primer elemento de la colección de modelos
        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos a Response objects
        $items = $collection->map(fn($model) => PendingExpenseResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
