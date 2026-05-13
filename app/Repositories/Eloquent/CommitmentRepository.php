<?php

namespace App\Repositories\Eloquent;

use App\Models\Commitment;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\CommitmentRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CommitmentRepository extends BaseRepository implements CommitmentRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function create(
        int $userId,
        int $costCenterId,
        int $eventId,
        float $commitmentAmount,
        string $frequencyType,
        string $currentStatus,
        int $createdBy
    ): Commitment
    {
        $cursorName = 'rs_Commitment';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"CommitmentsPkg"."CreateCommitment"',
                parameters: [ $userId, $costCenterId, $eventId, $commitmentAmount, $frequencyType, $currentStatus, $createdBy],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['UserId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['CostCenterId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['EventId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 4) {
                throw ValidationException::withMessages(['CommitmentAmount' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 5) {
                throw ValidationException::withMessages(['FrequencyType' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 6) {
                throw ValidationException::withMessages(['CurrentStatus' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }
            return $this->mapResultToModel($row, Commitment::class);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function getAll(
        ?int $id = null,
        ?int $userId = null,
        ?int $costCenterId = null,
        ?int $eventId = null,
        ?string $currentStatus = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection
    {
        $cursorName = 'rs_Commitments';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"CommitmentsPkg"."GetCommitments"',
                parameters: [ $id, $userId, $costCenterId, $eventId, $currentStatus, $pageSize, $pageNumber],
                cursorName: $cursorName
            );

            DB::commit();

            return $this->mapResultsToCollection($results, Commitment::class);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza un Commitment existente (campos opcionales).
     * @param int $id
     * @param int|null $userId
     * @param int|null $costCenterId
     * @param int|null $eventId
     * @param float|null $commitmentAmount
     * @param string|null $frequencyType
     * @param string|null $currentStatus
     * @param int $updatedBy
     * @return Commitment
     * @throws ValidationException
     */
    public function update(
        int $id,
        ?int $userId,
        ?int $costCenterId,
        ?int $eventId,
        ?float $commitmentAmount,
        ?string $frequencyType,
        ?string $currentStatus,
        int $updatedBy
    ): Commitment
    {
        $cursorName = 'rs_UpdateCommitment';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"CommitmentsPkg"."UpdateCommitment"',
                parameters: [$id, $userId, $costCenterId, $eventId, $commitmentAmount, $frequencyType, $currentStatus, $updatedBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) { throw new Exception("Sin respuesta de la base de datos."); }
            $row = $results[0];
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['UserId' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['CodeCostCenterId' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 3) { throw ValidationException::withMessages(['EventId'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId == 4) { throw ValidationException::withMessages(['CommitmentAmount'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId == 5) { throw ValidationException::withMessages(['FrequencyType'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId == 6) { throw ValidationException::withMessages(['CurrentStatus'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId == 7) { throw ValidationException::withMessages(['Id'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw ValidationException::withMessages(['Exception'=>[$row->ErrorMessage]]); }

            return $this->mapResultToModel($row, Commitment::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }
}
