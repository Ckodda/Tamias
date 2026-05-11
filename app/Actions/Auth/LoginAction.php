<?php

namespace App\Actions\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\Auth\LoginResponse;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\JWTGuard;

class LoginAction
{
    /**
     * Ejecuta el proceso de login.
     *
     * @param LoginRequest $request
     * @return LoginResponse
     * @throws ValidationException|Exception
     */
    public function execute(LoginRequest $request): LoginResponse
    {
        try {
            /** @var JWTGuard $guard */
            $guard = Auth::guard('api');

            // Mapeo interno: 'Email' y 'password' (para Laravel Auth)
            $authCredentials = [
                'Email' => $request->Email,
                'password' => $request->Password,
            ];

            if (!$token = $guard->attempt($authCredentials)) {
                throw ValidationException::withMessages([
                    'Email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            /** @var User $user */
            $user = $guard->user();

            return new LoginResponse(
                AccessToken: $token,
                TokenType: 'bearer',
                ExpiresIn: $guard->factory()->getTTL() * 60,
                User: [
                    'Id' => $user->Id,
                    'FullName' => $user->FullName,
                    'Email' => $user->Email,
                    'Roles' => $user->getRoleNames()->toArray(),
                ]
            );

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
