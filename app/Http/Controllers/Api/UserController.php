<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\CreateUserAction;
use App\Actions\User\GetUsersAction;
use App\Actions\User\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\GetUsersRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\PaginatedResponse;
use App\Http\Responses\User\UserResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use function Psy\debug;

class UserController extends Controller
{
    /**
     * Registra un nuevo Usuario.
     *
     * @param CreateUserRequest $request
     * @param CreateUserAction $action
     * @return ApiResponse<UserResponse>
     */
    public function store(CreateUserRequest $request, CreateUserAction $action): ApiResponse
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
        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }
    /**
     * Listado paginado de Usuarios.
     *
     * @param GetUsersRequest $request
     * @param GetUsersAction $action
     * @return ApiResponse<PaginatedResponse>
     */
    public function index(GetUsersRequest $request, GetUsersAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);

            return ApiResponse::success(
                Content: $results,
                Message: 'Listado de usuarios obtenido correctamente'
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }

    /**
     * Actualiza un Usuario existente.
     *
     * @param UpdateUserRequest $request
     * @param UpdateUserAction $action
     * @return ApiResponse<UserResponse>
     */
    public function update(UpdateUserRequest $request, UpdateUserAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Usuario actualizado exitosamente'
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
