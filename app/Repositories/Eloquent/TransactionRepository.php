<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    /**
     * Crea una nueva transacción ejecutando la lógica de orquestación en DB.
     *
     * @throws ValidationException|Exception
     */
    public function create(
        int $userId,
        int $costCenterId,
        int $currencyId,
        int $paymentMethodId,
        float $transactionAmount,
        string $transactionType,
        string $accountingPeriod,
        string $transactionDescription,
        int $createdBy,
        ?int $eventId = null,
        ?int $pendingExpenseId = null,
        ?int $loanId = null,
        float $appliedExchangeRate = 1.0,
        ?string $receiptImagePath = null
    ): Transaction {
        $cursorName = 'rs_Transaction';

        try {
            DB::beginTransaction();

            // IMPORTANTE: El orden de este array debe coincidir EXACTAMENTE con la firma del Procedure
            $results = $this->callProcedure(
                procedure: '"TransactionsPkg"."CreateTransaction"',
                parameters: [
                    $userId,
                    $costCenterId,
                    $currencyId,
                    $paymentMethodId,
                    $transactionAmount,
                    $transactionType,
                    $accountingPeriod,
                    $transactionDescription,
                    $createdBy,      // Parámetro obligatorio movido antes de los opcionales
                    $eventId,        // Opcionales empiezan aquí
                    $pendingExpenseId,
                    $loanId,
                    $appliedExchangeRate,
                    $receiptImagePath
                ],
                cursorName: $cursorName
            );

            if (empty($results)) {
                throw new Exception("No se recibió respuesta de la base de datos al registrar la transacción.");
            }

            $row = $results[0];

            // Manejo de errores de lógica de negocio del Procedure
            $this->handleProcedureErrors($row);

            DB::commit();

            return $this->mapResultToModel($row, Transaction::class);

        } catch (ValidationException | Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mapea los ErrorId del procedure a excepciones de validación de Laravel.
     * @throws ValidationException
     * @throws Exception
     */
    private function handleProcedureErrors(object $row): void
    {
        if ($row->ErrorId == 0) return;

        $errors = match ($row->ErrorId) {
            1 => ['CostCenterId' => [$row->ErrorMessage]],
            2 => ['CurrencyId' => [$row->ErrorMessage]],
            3 => ['PaymentMethodId' => [$row->ErrorMessage]],
            4 => ['TransactionAmount' => [$row->ErrorMessage]],
            5 => ['TransactionType' => [$row->ErrorMessage]],
            6 => ['PendingExpenseId' => [$row->ErrorMessage]],
            7 => ['PendingExpenseId' => ['Este gasto ya fue pagado previamente.']],
            8 => ['LoanId' => [$row->ErrorMessage]],
            9 => ['TransactionAmount' => ['El monto excede el saldo pendiente del préstamo.']],
            default => null,
        };

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        throw new Exception($row->ErrorMessage ?? "Error desconocido en el procedimiento de transacciones.");
    }

    /**
     * Anula una transacción y revierte sus efectos secundarios en la base de datos.
     *
     * @param int $id ID de la transacción a anular.
     * @param int $updatedBy ID del usuario que realiza la anulación.
     * @throws ValidationException|Exception
     */
    public function void(int $id, int $updatedBy): bool
    {
        $cursorName = 'rs_VoidTransaction';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"TransactionsPkg"."VoidTransaction"',
                parameters: [
                    $id,
                    $updatedBy
                ],
                cursorName: $cursorName
            );

            if (empty($results)) {
                throw new Exception("No se recibió respuesta al intentar anular la transacción.");
            }

            $row = $results[0];

            // Manejo de errores específicos del Procedure de anulación
            if ($row->ErrorId > 0) {
                $this->handleVoidErrors($row);
            }

            DB::commit();
            return true;

        } catch (ValidationException | Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Maneja los errores específicos del proceso de anulación.
     * @throws ValidationException
     * @throws Exception
     */
    private function handleVoidErrors(object $row): void
    {
        $errors = match ($row->ErrorId) {
            1 => ['TransactionId' => ['La transacción no existe en los registros.']],
            2 => ['TransactionId' => ['Esta transacción ya fue anulada previamente.']],
            default => null,
        };

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        throw new Exception($row->ErrorMessage ?? "Error crítico al anular la transacción.");
    }

    /**
     * Obtiene el listado de transacciones basado en filtros opcionales.
     *
     * @param string|null $startDate Fecha de inicio (Y-m-d)
     * @param string|null $endDate Fecha de fin (Y-m-d)
     * @param int|null $costCenterId ID del centro de costo
     * @param string|null $transactionType 'Income' o 'Expense'
     * @param int|null $userId ID del usuario relacionado
     * @param bool|null $isActive Estado de la transacción
     * @return array<Transaction>
     * @throws Exception
     */
    public function getAll(
        ?int $id = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $costCenterId = null,
        ?string $transactionType = null,
        ?int $userId = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection {
        $cursorName = 'rs_Transactions';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"TransactionsPkg"."GetTransactions"',
                parameters: [
                    $startDate,
                    $endDate,
                    $costCenterId,
                    $transactionType,
                    $userId,
                    $isActive,
                    $pageSize,
                    $pageNumber
                ],
                cursorName: $cursorName
            );
            DB::commit();
            return $this->mapResultsToCollection($results, Transaction::class);
        } catch (Exception $e) {
            DB::rollBack(); throw $e;
        }
    }
}
