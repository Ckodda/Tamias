<?php

namespace App\Http\Controllers\Api;

use App\Actions\PendingExpense\CreatePendingExpenseAction;
use App\Actions\PendingExpense\GetPendingExpensesAction;
use App\Actions\PendingExpense\UpdatePendingExpenseAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PendingExpense\CreatePendingExpenseRequest;
use App\Http\Requests\PendingExpense\GetPendingExpensesRequest;
use App\Http\Requests\PendingExpense\UpdatePendingExpenseRequest;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class PendingExpenseController extends Controller
{
    /**
     * Listado paginado de PendingExpenses
     */
    public function index(GetPendingExpensesRequest $request, GetPendingExpensesAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);
            return ApiResponse::success(Content: $results, Message: 'Listado obtenido correctamente');
        } catch (Exception $e) {
            return ApiResponse::error(Message: $e->getMessage(), Code: 500);
        }
    }

    /**
     * Registro de PendingExpense
     */
    public function store(CreatePendingExpenseRequest $request, CreatePendingExpenseAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'PendingExpense creado exitosamente', Code: 201);
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }

    /**
     * Actualización de PendingExpense
     */
    public function update(UpdatePendingExpenseRequest $request, UpdatePendingExpenseAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'PendingExpense actualizado exitosamente');
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }
}
