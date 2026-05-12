<?php

namespace App\Actions\Commitment;

use App\Http\Requests\Commitment\GetCommitmentsRequest;
use App\Http\Responses\Commitment\CommitmentResponse;
use App\Http\Responses\PaginatedResponse;
use App\Repositories\Contracts\CommitmentRepositoryInterface;

class GetCommitmentsAction
{
    public function __construct(
        protected CommitmentRepositoryInterface $repository
    )
    { }

    public function execute(GetCommitmentsRequest $request): PaginatedResponse
    {
        $collection = $this->repository->getAll(
            id: $request->Id,
            userId: $request->UserId,
            costCenterId: $request->CostCenterId,
            eventId: $request->EventId,
            currentStatus: $request->CurrentStatus,
            pageSize: $request->PageSize,
            pageNumber: $request->PageNumber
        );

        $totalCount = (int) ($collection->first()?->getAttribute('TotalCount') ?? 0);

        // Mapeamos cada item al objeto Response con lógica de visibilidad por roles
        $items = $collection->map(fn($model) => CommitmentResponse::fromModel($model));

        return PaginatedResponse::make(
            $items,
            $totalCount,
            $request->PageNumber,
            $request->PageSize
        );
    }
}
