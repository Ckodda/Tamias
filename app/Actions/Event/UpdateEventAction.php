<?php

namespace App\Actions\Event;

use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Responses\Event\EventResponse;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class UpdateEventAction
{
    public function __construct(
        protected EventRepositoryInterface $repository
    ) {}

    /**
     * @throws \Exception
     */
    public function execute(UpdateEventRequest $request): EventResponse
    {
        try {
            $userId = Auth::id();

            $event = $this->repository->update(
                id: $request->Id,
                costCenterId: $request->CostCenterId,
                currencyId: $request->CurrencyId,
                eventName: $request->EventName,
                targetAmount: $request->TargetAmount,
                eventStatus: $request->EventStatus,
                startDate: $request->StartDate,
                isActive: $request->IsActive,
                updatedBy: $userId
            );

            return EventResponse::fromModel($event);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
