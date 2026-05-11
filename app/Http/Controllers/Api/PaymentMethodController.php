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
use Exception;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function index(GetPaymentMethodsRequest $request, GetPaymentMethodsAction $action): ApiResponse
    {
        try {
            return ApiResponse::success(Content: $action->execute($request), Message: 'Listado obtenido correctamente');
        } catch (Exception $e) { return ApiResponse::error(Message: $e->getMessage(), Code: 500); }
    }

    public function store(CreatePaymentMethodRequest $request, CreatePaymentMethodAction $action): ApiResponse
    {
        try {
            return ApiResponse::success(Content: $action->execute($request), Message: 'Método de pago creado exitosamente', Code: 201);
        } catch (ValidationException $e) { return ApiResponse::error(Message: 'Error de validación', Code: 422, Content: $e->errors());
        } catch (Exception $e) { return ApiResponse::error(Message: $e->getMessage(), Code: 500); }
    }

    public function update(UpdatePaymentMethodRequest $request, UpdatePaymentMethodAction $action): ApiResponse
    {
        try {
            return ApiResponse::success(Content: $action->execute($request), Message: 'Método de pago actualizado exitosamente');
        } catch (ValidationException $e) { return ApiResponse::error(Message: 'Error de validación', Code: 422, Content: $e->errors());
        } catch (Exception $e) { return ApiResponse::error(Message: $e->getMessage(), Code: 500); }
    }
}
