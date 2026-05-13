<?php

namespace App\Repositories\Eloquent;

use App\Models\CostCenter;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\CostCenterRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class CostCenterRepository extends BaseRepository implements CostCenterRepositoryInterface
{
    /**
     * @throws ValidationException|Exception
     */
    public function create(string $codeCostCenter, string $centerName, ?int $createdBy = null): CostCenter
    {
        $cursorName = 'rs_CostCenter';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"CostCentersPkg"."CreateCostCenter"',
                parameters: [$centerName, $codeCostCenter, $createdBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) { throw new Exception("Sin respuesta de la base de datos."); }

            $row = $results[0];
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['CenterName' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['CodeCostCenter' => [$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw new Exception($row->ErrorMessage); }

            return $this->mapResultToModel($row, CostCenter::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }

    /**
     * @throws Exception
     */
    public function getAll(?int $id = null, ?string $centerName = null, ?string $codeCostCenter = null, ?bool $isActive = null, int $pageSize = 10, int $pageNumber = 1): Collection
    {
        $cursorName = 'rs_CostCenters';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"CostCentersPkg"."GetCostCenters"',
                parameters: [$id, $centerName, $codeCostCenter, $isActive, $pageSize, $pageNumber],
                cursorName: $cursorName
            );
            DB::commit();
            return $this->mapResultsToCollection($results, CostCenter::class);
        } catch (Exception $e) { DB::rollBack(); throw $e; }
    }

    /**
     * Actualiza un centro de costo existente (campos opcionales).
     * @throws ValidationException|Exception
     */
    public function update(
        int $id,
        ?string $codeCostCenter = null,
        ?string $centerName = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): CostCenter
    {
        $cursorName = 'rs_UpdateCostCenter';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"CostCentersPkg"."UpdateCostCenter"',
                parameters: [$id, $centerName, $codeCostCenter, $isActive, $updatedBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) { throw new Exception("Sin respuesta de la base de datos."); }
            $row = $results[0];
            if ($row->ErrorId == 1) { throw ValidationException::withMessages(['CenterName' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 2) { throw ValidationException::withMessages(['CodeCostCenter' => [$row->ErrorMessage]]); }
            if ($row->ErrorId == 3) { throw ValidationException::withMessages(['Id'=>[$row->ErrorMessage]]); }
            if ($row->ErrorId > 0) { throw ValidationException::withMessages(['Exception'=>[$row->ErrorMessage]]); }

            return $this->mapResultToModel($row, CostCenter::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }
}
