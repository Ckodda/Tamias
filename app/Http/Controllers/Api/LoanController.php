<?php

namespace App\Http\Controllers\Api;

use App\Actions\Loans\CreateLoanAction;
use App\Actions\Loans\GetLoansAction;
use App\Actions\Loans\UpdateLoanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\CreateLoanRequest;
use App\Http\Requests\Loan\GetLoansRequest;
use App\Http\Requests\Loan\UpdateLoanRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Loan\LoanResponse;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
    /**
     * Lista paginada de Loans
     *
     * @param GetLoansRequest $request
     * @param GetLoansAction $action
     * @return ApiResponse<LoanResponse>
     */
    public function index(GetLoansRequest $request, GetLoansAction $action):ApiResponse
    {
        try{
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Evento registrado exitosamente',
                Code: 201
            );
        } catch (\Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
    /**
     * Registra un nuevo Loan.
     *
     * @param CreateLoanRequest $request
     * @param CreateLoanAction $action
     * @return ApiResponse<LoanResponse>
     */
    public function store(CreateLoanRequest $request, CreateLoanAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Evento registrado exitosamente',
                Code: 201
            );

        } catch (ValidationException $exception) {
            return ApiResponse::error(
                Message: 'Los datos proporcionados no son válidos.',
                Code: 422,
                Content: $exception->errors()
            );
        } catch (\Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
    /**
     * Actualiza un nuevo Loan.
     *
     * @param UpdateLoanRequest $request
     * @param UpdateLoanAction $action
     * @return ApiResponse<LoanResponse>
     */
    public function update(UpdateLoanRequest $request, UpdateLoanAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Loan actualizado exitosamente',
                Code: 201
            );

        } catch (ValidationException $exception) {
            return ApiResponse::error(
                Message: 'Los datos proporcionados no son válidos.',
                Code: 422,
                Content: $exception->errors()
            );
        } catch (\Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
}
