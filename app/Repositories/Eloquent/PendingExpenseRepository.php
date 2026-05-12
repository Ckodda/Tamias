<?php

namespace App\Repositories\Eloquent;

use App\Models\PendingExpense;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\PendingExpenseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PendingExpenseRepository extends BaseRepository implements PendingExpenseRepositoryInterface
{
    /**
     * Registro de un nuevo gasto pendiente.
     * @throws ValidationException|Exception
     */
    public function create(
        int $costCenterId,
        string $expenseDescription,
        float $totalAmount,
        string $dueDate,
        string $providerName,
        string $paymentStatus,
        ?int $createdBy = null
    ): PendingExpense {
        $cursorName = 'rs_PendingExpense';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PendingExpensesPkg"."CreatePendingExpense"',
                parameters: [
                    $costCenterId,
                    $expenseDescription,
                    $totalAmount,
                    $dueDate,
                    $providerName,
                    $paymentStatus,
                    $createdBy
                ],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos al crear el gasto.");
            }

            $row = $results[0];

            // Mapeo de errores según los ErrorId definidos en el Procedure
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['CostCenterId' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['TotalAmount' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 3) { throw ValidationException::withMessages(['PaymentStatus' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 4) { throw ValidationException::withMessages(['ExpenseDescription' => [$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw new Exception($row->ErrorMessage); }

            return $this->mapResultToModel($row, PendingExpense::class);
        } catch (ValidationException | Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualización de un gasto existente.
     * @throws ValidationException|Exception
     */
    public function update(
    int $id,
    ?int $costCenterId = null,
    ?string $expenseDescription = null,
    ?float $totalAmount = null,
    ?string $dueDate = null,
    ?string $providerName = null,
    ?string $paymentStatus = null,
    ?bool $isActive = null,
    ?int $updatedBy = null
    ): PendingExpense {
        $cursorName = 'rs_UpdatePendingExpense';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PendingExpensesPkg"."UpdatePendingExpense"',
                parameters: [
                    $id,
                    $costCenterId,
                    $expenseDescription,
                    $totalAmount,
                    $dueDate,
                    $providerName,
                    $paymentStatus,
                    $isActive,
                    $updatedBy
                ],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos al actualizar el gasto.");
            }

            $row = $results[0];

            // Mapeo de errores para actualización
            if ($row->ErrorId == 5) { throw ValidationException::withMessages(['Id' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['CostCenterId' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['TotalAmount' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 3) { throw ValidationException::withMessages(['PaymentStatus' => [$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw new Exception($row->ErrorMessage); }

            return $this->mapResultToModel($row, PendingExpense::class);
        } catch (ValidationException | Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Listado filtrado y paginado.
     * @throws Exception
     */
    public function getAll(
        ?int $id = null,
        ?int $costCenterId = null,
        ?string $paymentStatus = null,
        ?string $providerName = null,
        ?string $startDate = null,
        ?string $endDate = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection {
        $cursorName = 'rs_PendingExpenses';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PendingExpensesPkg"."GetPendingExpenses"',
                parameters: [
                    $id,
                    $costCenterId,
                    $paymentStatus,
                    $providerName,
                    $startDate,
                    $endDate,
                    $pageSize,
                    $pageNumber
                ],
                cursorName: $cursorName
            );
            DB::commit();

            return $this->mapResultsToCollection($results, PendingExpense::class);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
