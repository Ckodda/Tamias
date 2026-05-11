<?php

namespace App\Actions\Currency;

use App\Http\Requests\Currency\GetCurrenciesRequest;
use App\Http\Responses\Currency\CurrencyResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\CurrencyRepositoryInterface;

class GetCurrenciesAction
{
    public function __construct(
        protected CurrencyRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la lógica para obtener la lista de monedas paginada.
     *
     * @param GetCurrenciesRequest $request
     * @return PaginatedResponse
     */
    public function execute(GetCurrenciesRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            currencyName: $request->CurrencyName,
            currencyCode: $request->CurrencyCode,
            isActive: $request->IsActive,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($currency) => CurrencyResponse::fromModel($currency));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
