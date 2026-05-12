<?php

namespace App\Http\Controllers\Api;

use App\Actions\Loans\CreateLoanAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Loan\CreateLoanRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Loan\LoanResponse;
use Illuminate\Validation\ValidationException;

class LoanController extends Controller
{
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
}
