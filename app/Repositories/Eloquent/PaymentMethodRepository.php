<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentMethod;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class PaymentMethodRepository extends BaseRepository implements PaymentMethodRepositoryInterface
{
    public function create(string $methodName, ?int $createdBy = null): PaymentMethod
    {
        $cursorName = 'rs_PaymentMethod';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PaymentMethodsPkg"."CreatePaymentMethod"',
                parameters: [$methodName, $createdBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) throw new Exception("Sin respuesta de la base de datos.");
            $row = $results[0];

            if ($row->ErrorId == 1) throw ValidationException::withMessages(['MethodName' => [$row->ErrorMessage]]);
            if ($row->ErrorId > 0) throw new Exception($row->ErrorMessage);

            return $this->mapResultToModel($row, PaymentMethod::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }

    public function getAll(?int $id = null, ?string $methodName = null, ?bool $isActive = null, int $pageSize = 10, int $pageNumber = 1): Collection
    {
        $cursorName = 'rs_PaymentMethods';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PaymentMethodsPkg"."GetPaymentMethods"',
                parameters: [$id, $methodName, $isActive, $pageSize, $pageNumber],
                cursorName: $cursorName
            );
            DB::commit();
            return $this->mapResultsToCollection($results, PaymentMethod::class);
        } catch (Exception $e) { DB::rollBack(); throw $e; }
    }

    public function update(int $id, ?string $methodName = null, ?bool $isActive = null, ?int $updatedBy = null): PaymentMethod
    {
        $cursorName = 'rs_UpdatePaymentMethod';
        try {
            DB::beginTransaction();
            $results = $this->callProcedure(
                procedure: '"PaymentMethodsPkg"."UpdatePaymentMethod"',
                parameters: [$id, $methodName, $isActive, $updatedBy],
                cursorName: $cursorName
            );
            DB::commit();

            if (empty($results)) throw new Exception("Sin respuesta de la base de datos.");
            $row = $results[0];

            if ($row->ErrorId == 1) throw ValidationException::withMessages(['MethodName' => [$row->ErrorMessage]]);
            if ($row->ErrorId == 3) throw ValidationException::withMessages(['Id' => [$row->ErrorMessage]]);
            if ($row->ErrorId > 0) throw new Exception($row->ErrorMessage);

            return $this->mapResultToModel($row, PaymentMethod::class);
        } catch (ValidationException|Exception $e) { DB::rollBack(); throw $e; }
    }
}
