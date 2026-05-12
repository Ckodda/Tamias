<?php

namespace App\Actions\Commitment;

use App\Http\Requests\Commitment\CreateCommitmentRequest;
use App\Http\Requests\Commitment\UpdateCommitmentRequest;
use App\Http\Responses\Commitment\CommitmentResponse;
use App\Repositories\Contracts\CommitmentRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UpdateCommitmentAction
{
    public function __construct(
        protected CommitmentRepositoryInterface $repository
    )
    { }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function execute(UpdateCommitmentRequest $request): CommitmentResponse
    {
        try {
            $userId = Auth::id();

            $costCenter = $this->repository->update(
                id: $request->Id,
                userId: $request->UserId,
                costCenterId: $request->CostCenterId,
                eventId: $request->EventId,
                commitmentAmount: $request->CommitmentAmount,
                frequencyType: $request->FrequencyType,
                currentStatus: $request->CurrentStatus,
                updatedBy: $userId
            );

            return CommitmentResponse::fromModel($costCenter);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
