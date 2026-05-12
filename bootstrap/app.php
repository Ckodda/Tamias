<?php

use App\Http\Middleware\ForceJsonResponse;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: [
            __DIR__.'/../routes/api.php',
            __DIR__.'/../routes/api/Transactions.php',
            __DIR__.'/../routes/api/CostCenters.php',
            __DIR__.'/../routes/api/Currencies.php',
            __DIR__.'/../routes/api/Events.php',
            __DIR__.'/../routes/api/PaymentMethods.php',
            __DIR__.'/../routes/api/Users.php',
            __DIR__.'/../routes/api/Loans.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(append: [
            ForceJsonResponse::class,
        ]);

        $middleware->redirectTo(
            guests: fn (Request $request) => $request->is('api/*') ? null : route('login')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->shouldRenderJsonWhen(function (Request $request, $e) {
            return $request->is('api/*');
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return ApiResponse::error(
                Message: 'No autorizado. Token inválido o no proporcionado.',
                Code: 401
            );
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            return ApiResponse::error(
                Message: 'Los datos proporcionados no son válidos.',
                Code: 422,
                Content: $e->errors()
            );
        });

        $exceptions->render(function (Throwable $e,Request $request){
           return ApiResponse::error(
               Message: $e->getMessage(),
               Code: 500,
               Content: $e->getCode()
           );
        });

    })->create();
