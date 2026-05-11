<?php

namespace App\Actions\PaymentMethod;

use App\Http\Requests\PaymentMethod\GetPaymentMethodsRequest;
use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;

class GetPaymentMethodsAction
{
    public function __construct(
        protected PaymentMethodRepositoryInterface $repository
    ) {}

    public function execute(GetPaymentMethodsRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            methodName: $request->MethodName,
            isActive: $request->IsActive,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        // Extraemos el TotalCount del primer elemento de la colección de modelos
        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos a Response objects
        $items = $collection->map(fn($model) => PaymentMethodResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
