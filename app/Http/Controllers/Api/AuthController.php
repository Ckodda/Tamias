<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Auth\LoginResponse;
use Illuminate\Validation\ValidationException;
use Exception;
use Tymon\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    /**
     * Maneja la solicitud de login.
     *
     * @param LoginRequest $request
     * @param LoginAction $loginAction
     * @return ApiResponse<LoginResponse>
     */
    public function login(LoginRequest $request, LoginAction $loginAction): ApiResponse
    {
        try {
            $response = $loginAction->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Inicio de sesión exitoso'
            );

        } catch (ValidationException $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
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
     * Cierra la sesión.
     *
     * @return ApiResponse<null>
     */
    public function logout(): ApiResponse
    {
        /** @var JWTGuard $guard */
        $guard = auth()->guard('api');
        $guard->logout();

        return ApiResponse::success(Message: 'Sesión cerrada exitosamente');
    }
}
