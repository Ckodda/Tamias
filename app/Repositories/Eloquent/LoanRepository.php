<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Models\Loan;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\LoanRepositoryInterface;
use Exception;
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

}
