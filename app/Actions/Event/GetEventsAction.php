<?php

namespace App\Actions\Event;

use App\Http\Requests\Event\GetEventsRequest;
use App\Http\Responses\Event\EventResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\EventRepositoryInterface;

class GetEventsAction
{
    public function __construct(
        protected EventRepositoryInterface $repository
    ) {}

    /**
     * Ejecuta la lógica para obtener la lista de eventos paginada.
     *
     * @param GetEventsRequest $request
     * @return PaginatedResponse
     */
    public function execute(GetEventsRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            eventName: $request->EventName,
            currencyId: $request->CurrencyId,
            startDate: $request->StartDate,
            isActive: $request->IsActive,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($event) => EventResponse::fromModel($event));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
