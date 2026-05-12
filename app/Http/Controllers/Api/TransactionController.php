<?php

namespace App\Http\Controllers\Api;

use App\Actions\CostCenter\CreateCostCenterAction;
use App\Actions\CostCenter\GetCostCentersAction;
use App\Actions\CostCenter\UpdateCostCenterAction;
use App\Actions\Transaction\CreateTransactionAction;
use App\Actions\Transaction\GetTransactionsAction;
use App\Actions\Transaction\VoidTransactionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CostCenter\CreateCostCenterRequest;
use App\Http\Requests\CostCenter\GetCostCentersRequest;
use App\Http\Requests\CostCenter\UpdateCostCenterRequest;
use App\Http\Requests\Transaction\CreateTransactionRequest;
use App\Http\Requests\Transaction\GetTransactionsRequest;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Listado paginado de Centros de Costo.
     */
    public function index(GetTransactionsRequest $request, GetTransactionsAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);
            return ApiResponse::success(Content: $results, Message: 'Listado obtenido correctamente');
        } catch (Exception $e) {
            return ApiResponse::error(Message: $e->getMessage(), Code: 500);
        }
    }

    /**
     * Registro de Transaction.
     */
    public function store(CreateTransactionRequest $request, CreateTransactionAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'Transaction creado exitosamente', Code: 201);
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }

    /**
     * Anulación de Transaction.
     *
     * Se usa el ID de la transacción para revertir sus efectos.
     */
    public function void(int $id, VoidTransactionAction $action): ApiResponse
    {
        try {
            $action->execute($id);
            return ApiResponse::success(Message: 'Transacción anulada y saldos revertidos exitosamente.', Code: 201);
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'No se pudo realizar la anulación.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }

}
