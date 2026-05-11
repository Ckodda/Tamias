<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return JsonResponse|Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception): JsonResponse|Response
    {
        if ($request->expectsJson()) {
            return $this->handleApiException($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Handle API exceptions and return a custom JSON response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return JsonResponse
     */
    protected function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        $statusCode = $this->isHttpException($exception) ? $exception->getStatusCode() : 500;
        $message = $exception->getMessage();
        $content = null;

        if ($exception instanceof ValidationException) {
            $statusCode = 422; // Unprocessable Entity
            $message = 'Validation Error';
            $content = $exception->errors();
        } elseif ($exception instanceof HttpException) {
            // For other HTTP exceptions (404, 403, etc.)
            $message = $exception->getMessage();
        } elseif ($statusCode === 500) {
            // Generic 500 error, hide detailed message in production
            $message = config('app.debug') ? $message : 'An unexpected error occurred.';
        }

        return response()->json([
            'Code' => $statusCode,
            'Message' => $message,
            'Content' => $content,
        ], $statusCode);
    }
}
