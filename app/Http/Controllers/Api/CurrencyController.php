<?php

namespace App\Http\Controllers\Api;

use App\Actions\Currency\CreateCurrencyAction;
use App\Actions\Currency\GetCurrenciesAction;
use App\Actions\Currency\UpdateCurrencyAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Currency\CreateCurrencyRequest;
use App\Http\Requests\Currency\GetCurrenciesRequest;
use App\Http\Requests\Currency\UpdateCurrencyRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Currency\CurrencyResponse;
use App\Http\Responses\PaginatedResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class CurrencyController extends Controller
{
    /**
     * Listado paginado de Monedas.
     *
     * @param GetCurrenciesRequest $request
     * @param GetCurrenciesAction $action
     * @return ApiResponse
     */
    public function index(GetCurrenciesRequest $request, GetCurrenciesAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);

            return ApiResponse::success(
                Content: $results,
                Message: 'Listado de monedas obtenido correctamente'
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }

    /**
     * Registra una nueva Moneda.
     *
     * @param CreateCurrencyRequest $request
     * @param CreateCurrencyAction $action
     * @return ApiResponse
     */
    public function store(CreateCurrencyRequest $request, CreateCurrencyAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Moneda registrada exitosamente',
                Code: 201
            );

        } catch (ValidationException $exception) {
            return ApiResponse::error(
                Message: 'Los datos proporcionados no son válidos.',
                Code: 422,
                Content: $exception->errors()
            );
        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }

    /**
     * Actualiza una Moneda existente.
     *
     * @param UpdateCurrencyRequest $request
     * @param UpdateCurrencyAction $action
     * @return ApiResponse
     */
    public function update(UpdateCurrencyRequest $request, UpdateCurrencyAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Moneda actualizada exitosamente'
            );

        } catch (ValidationException $exception) {
            return ApiResponse::error(
                Message: 'Los datos proporcionados no son válidos.',
                Code: 422,
                Content: $exception->errors()
            );
        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
}
