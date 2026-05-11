<?php

namespace App\Http\Controllers\Api;

use App\Actions\PaymentMethod\CreatePaymentMethodAction;
use App\Actions\PaymentMethod\GetPaymentMethodsAction;
use App\Actions\PaymentMethod\UpdatePaymentMethodAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethod\CreatePaymentMethodRequest;
use App\Http\Requests\PaymentMethod\GetPaymentMethodsRequest;
use App\Http\Requests\PaymentMethod\UpdatePaymentMethodRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\PaymentMethod\PaymentMethodResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    /**
     * Listado paginado de Metodos de pago.
     *
     * @param GetPaymentMethodsRequest $request
     * @param GetPaymentMethodsAction $action
     * @return ApiResponse<PaginatedResponse>
     */
    public function index(GetPaymentMethodsRequest $request, GetPaymentMethodsAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);

            return ApiResponse::success(
                Content: $results,
                Message: 'Listado de metodos de pago obtenido correctamente'
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
    /**
     * Registra un nuevo Metodo de pago.
     *
     * @param CreatePaymentMethodRequest $request
     * @param CreatePaymentMethodAction $action
     * @return ApiResponse<PaymentMethodResponse>
     */
    public function store(CreatePaymentMethodRequest $request, CreatePaymentMethodAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Metodo de pago registrado exitosamente',
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
     * Actualiza un Metodo de pago existente.
     *
     * @param UpdatePaymentMethodRequest $request
     * @param UpdatePaymentMethodAction $action
     * @return ApiResponse<PaymentMethodResponse>
     */
    public function update(UpdatePaymentMethodRequest $request, UpdatePaymentMethodAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Metodo de pago actualizado exitosamente'
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
