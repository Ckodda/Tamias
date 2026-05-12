<?php

namespace App\Http\Controllers\Api;

use App\Actions\Commitment\CreateCommitmentAction;
use App\Actions\Commitment\GetCommitmentsAction;
use App\Actions\Commitment\UpdateCommitmentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Commitment\CreateCommitmentRequest;
use App\Http\Requests\Commitment\GetCommitmentsRequest;
use App\Http\Requests\Commitment\UpdateCommitmentRequest;
use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class CommitmentController extends Controller
{
    /**
     * Listado paginado de Commitments
     */
    public function index(GetCommitmentsRequest $request, GetCommitmentsAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);
            return ApiResponse::success(Content: $results, Message: 'Listado obtenido correctamente');
        } catch (Exception $e) {
            return ApiResponse::error(Message: $e->getMessage(), Code: 500);
        }
    }

    /**
     * Registro de Commitments.
     */
    public function store(CreateCommitmentRequest $request, CreateCommitmentAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'Commitment creado exitosamente', Code: 201);
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }

    /**
     * Actualización de Commitments.
     */
    public function update(UpdateCommitmentRequest $request, UpdateCommitmentAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);
            return ApiResponse::success(Content: $response, Message: 'Commitment actualizado exitosamente');
        } catch (ValidationException $exception) {
            return ApiResponse::error(Message: 'Los datos proporcionados no son válidos.', Code: 422, Content: $exception->errors());
        } catch (Exception $exception) {
            return ApiResponse::error(Message: $exception->getMessage(), Code: 500);
        }
    }
}
