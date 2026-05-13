<?php

namespace App\Repositories\Eloquent;

use App\Models\MonthlyBalance;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\MonthlyBalanceRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MonthlyBalanceRepository extends BaseRepository implements MonthlyBalanceRepositoryInterface
{

    /**
     * @throws Exception
     */
    public function getAll(?int $costCenterId, ?string $startMonth, ?string $endMonth, int $pageSize, int $pageNumber): Collection
    {
        $cursorName = 'rs_MonthlyBalances';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"ReportsPkg"."GetMonthlyBalances"',
                parameters: [
                    $costCenterId,
                    $startMonth,
                    $endMonth,
                    $pageSize,
                    $pageNumber
                ],
                cursorName: $cursorName
            );
            DB::commit();
            return $this->mapResultsToCollection($results, MonthlyBalance::class);
        } catch (Exception $e) { DB::rollBack(); throw $e; }
    }
}
