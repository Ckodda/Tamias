<?php

namespace App\Http\Controllers\Api;

use App\Actions\CostCenter\CreateCostCenterAction;
use App\Actions\CostCenter\GetCostCentersAction;
use App\Actions\CostCenter\UpdateCostCenterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CostCenter\CreateCostCenterRequest;
use App\Http\Requests\CostCenter\GetCostCentersRequest;
use App\Http\Requests\CostCenter\UpdateCostCenterRequest;
use App\Http\Responses\ApiResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class CostCenterController extends Controller
{
    /**
     * Listado paginado de Centros de Costo.
     */
    public function index(GetCostCentersRequest $request, GetCostCentersAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);
            return ApiResponse::success(Content: $results, Message: 'Listado obtenido correctamente');
        } catch (Exception $e) {
            return ApiResponse::error(Message: $e->getMessage(), Code: 500);
        }
    }

    /**
     * Registro de Centro de Costo.
     */
    public function store(CreateCostCenterRequest $request, CreateCostCenterAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'Centro de Costo creado exitosamente', Code: 201);
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }

    /**
     * Actualización de Centro de Costo.
     */
    public function update(UpdateCostCenterRequest $request, UpdateCostCenterAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'Centro de Costo actualizado exitosamente');
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }
}
