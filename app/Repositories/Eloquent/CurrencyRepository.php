<?php

namespace App\Repositories\Eloquent;

use App\Models\Currency;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\CurrencyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    /**
     * @throws ValidationException|Exception
     */
    public function create(
        string $currencyName,
        string $currencyCode,
        string $currencySymbol,
        float $exchangeRate,
        ?int $createdBy = null
    ): Currency {
        $cursorName = 'rs_Currency';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"CurrenciesPkg"."CreateCurrency"',
                parameters: [$currencyName, $currencyCode, $currencySymbol, $exchangeRate, $createdBy],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['CurrencyName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 3) {
                throw ValidationException::withMessages(['CurrencyCode' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Currency::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtiene todas las monedas con filtros y paginación.
     * @throws Exception
     */
    public function getAll(
        ?int $id = null,
        ?string $currencyName = null,
        ?string $currencyCode = null,
        ?bool $isActive = null,
        int $pageSize = 10,
        int $pageNumber = 1
    ): Collection {
        $cursorName = 'rs_Currencies';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"CurrenciesPkg"."GetCurrencies"',
                parameters: [ $id, $currencyName, $currencyCode, $isActive, $pageSize, $pageNumber],
                cursorName: $cursorName
            );

            DB::commit();

            return $this->mapResultsToCollection($results, Currency::class);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Actualiza una moneda existente.
     * @throws ValidationException|Exception
     */
    public function update(
        int $id,
        ?string $currencyName = null,
        ?string $currencyCode = null,
        ?string $currencySymbol = null,
        ?float $exchangeRate = null,
        ?bool $isActive = null,
        ?int $updatedBy = null
    ): Currency {
        $cursorName = 'rs_UpdateCurrency';

        try {
            DB::beginTransaction();

            $results = $this->callProcedure(
                procedure: '"CurrenciesPkg"."UpdateCurrency"',
                parameters: [$id, $currencyName, $currencyCode, $currencySymbol, $exchangeRate, $isActive, $updatedBy],
                cursorName: $cursorName
            );

            DB::commit();

            if (empty($results)) {
                throw new Exception("Sin respuesta de la base de datos.");
            }

            $row = $results[0];

            // Manejo de errores basado en el SP (1: Nombre, 2: Código, 4: No existe)
            if ($row->ErrorId == 1) {
                throw ValidationException::withMessages(['CurrencyName' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 2) {
                throw ValidationException::withMessages(['CurrencyCode' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId == 4) {
                throw ValidationException::withMessages(['Id' => [$row->ErrorMessage]]);
            }
            if ($row->ErrorId > 0) {
                throw new Exception($row->ErrorMessage);
            }

            return $this->mapResultToModel($row, Currency::class);

        } catch (ValidationException|Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
