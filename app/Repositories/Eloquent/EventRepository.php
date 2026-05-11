<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    /**
     * @throws ValidationException|Exception
     */
    public function create(
        int $costCenterId,
        int $currencyId,
        string $eventName,
        float $targetAmount,
        string $eventStatus,
        string $startDate,
        ?int $createdBy = null
    ): Event {
        $cursorName = 'rs_Event';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"EventsPkg"."CreateEvent"',
                parameters: [$costCenterId, $currencyId, $eventName, $targetAmount, $eventStatus, $startDate, $createdBy],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['CostCenterId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['EventName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['CurrencyId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Event::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene eventos con filtros y paginación.
     */
    public function getAll(
        ?int $id = null,
        ?string $eventName = null,
        ?int $currencyId = null,
        ?string $startDate = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection {
        $cursorName = 'rs_Events';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"EventsPkg"."GetEvents"',
                parameters: [$id, $eventName, $currencyId, $startDate, $isActive, $pageSize, $pageNumber],
                cursorName: $cursorName
            );

            DB::commit();

            return $this->mapResultsToCollection($results, Event::class);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza un evento existente.
     * @throws ValidationException|Exception
     */
    public function update(
        int $id,
        ?int $costCenterId = null,
        ?int $currencyId = null,
        ?string $eventName = null,
        ?float $targetAmount = null,
        ?string $eventStatus = null,
        ?string $startDate = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): Event {
        $cursorName = 'rs_UpdateEvent';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"EventsPkg"."UpdateEvent"',
                parameters: [$id, $costCenterId, $currencyId, $eventName, $targetAmount, $eventStatus, $startDate, $isActive, $updatedBy],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            // Manejo de errores basado en el SP
            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['CostCenterId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['EventName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['CurrencyId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 4) {
                throw ValidationException::withMessages(['Id' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Event::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
