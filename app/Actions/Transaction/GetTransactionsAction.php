<?php

namespace App\Actions\Transaction;

use App\Http\Requests\Transaction\GetTransactionsRequest;
use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use App\Http\Responses\Transaction\TransactionResponse;
use App\Repositories\Contracts\TransactionRepositoryInterface;

class GetTransactionsAction
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
    )
    { }

    public function execute(GetTransactionsRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            startDate: $request->StartDate,
            endDate: $request->EndDate,
            costCenterId: $request->CostCenterId,
            transactionType: $request->TransactionType,
            userId: $request->UserId,
            isActive: $request->IsActive
        );
        // Extraemos el TotalCount del primer elemento de la colección de modelos
        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        $items = $collection->map(fn($model) => TransactionResponse::fromModel($model));

        // Transformamos cada modelo de la colección al formato de respuesta
        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
