<?php

namespace App\Actions\Event;

use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Responses\Event\EventResponse;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CreateEventAction
{
    public function __construct(
        protected EventRepositoryInterface $repository
    ) {}

    /**
     * @throws \Exception
     */
    public function execute(CreateEventRequest $request): EventResponse
    {
        try {
            $userId = Auth::id();

            $event = $this->repository->create(
                costCenterId: $request->CostCenterId,
                currencyId: $request->CurrencyId,
                eventName: $request->EventName,
                targetAmount: $request->TargetAmount,
                eventStatus: $request->EventStatus,
                startDate: $request->StartDate,
                createdBy: $userId
            );

            return EventResponse::fromModel($event);

        } catch (\Exception $e) {
            throw $e;
        }
    }
}
