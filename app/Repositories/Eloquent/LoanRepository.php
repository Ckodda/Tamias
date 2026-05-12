<?php

namespace App\Repositories\Eloquent;

use App\Models\Loan;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{
    /**
     * @throws ValidationException|Exception
     */
    public function create(
        string $lenderName,
        float $principalAmount,
        float $interestAmount,
        float $totalToRepay,
        string $repaymentDueDate,
        string $loanStatus,
        bool $isActive,
        int $currencyId,
        int $createdBy,
        int $updatedBy
    ): Loan {
        $cursorName = 'rs_Loan';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"LoansPkg"."CreateLoan"',
                parameters: [
                    $lenderName,
                    $principalAmount,
                    $interestAmount,
                    $totalToRepay,
                    $repaymentDueDate,
                    $loanStatus,
                    $isActive,
                    $currencyId,
                    $createdBy,
                    $updatedBy
                ],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['LenderName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['PrincipalAmount' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['CurrencyId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Loan::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function getAll(
        ?int $id = null,
        ?string $lenderName = null,
        ?int $currencyId = null,
        ?string $repaymentDueDate = null,
        ?bool $isActive = null,
        ?string $loanStatus = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection {
        $cursorName = 'rs_Loans';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"LoansPkg"."GetLoans"',
                parameters: [$id, $lenderName, $currencyId, $repaymentDueDate, $isActive, $loanStatus, $pageSize, $pageNumber],
                cursorName: $cursorName
            );

            DB::commit();

            return $this->mapResultsToCollection($results, Loan::class);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(int $id, ?string $lenderName, ?float $principalAmount, ?float $interestAmount, ?float $totalToRepay, ?string $repaymentDueDate, ?string $loanStatus, ?bool $isActive, ?int $currencyId, int $updatedBy): Loan
    {
        $cursorName = 'rs_UpdateLoan';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"LoansPkg"."UpdateLoan"',
                parameters: [
                    $id,
                    $lenderName,
                    $principalAmount,
                    $interestAmount,
                    $totalToRepay,
                    $repaymentDueDate,
                    $loanStatus,
                    $isActive,
                    $currencyId,
                    $updatedBy
                ],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['LenderName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['PrincipalAmount' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['CurrencyId' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Loan::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
