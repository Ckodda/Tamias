<?php

namespace App\Actions\Commitment;

use App\Http\Requests\Commitment\CreateCommitmentRequest;
use App\Http\Responses\Commitment\CommitmentResponse;
use App\Repositories\Contracts\CommitmentRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateCommitmentAction
{
    public function __construct(
        protected CommitmentRepositoryInterface $repository
    )
    { }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function execute(CreateCommitmentRequest $request): CommitmentResponse
    {
        try {
            $userId = Auth::id();

            $commitment = $this->repository->create(
                userId: $request->UserId,
                costCenterId: $request->CostCenterId,
                eventId: $request->EventId,
                commitmentAmount: $request->CommitmentAmount,
                frequencyType: $request->FrequencyType,
                currentStatus: $request->CurrentStatus,
                createdBy: $userId
            );

            return CommitmentResponse::fromModel($commitment);

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
