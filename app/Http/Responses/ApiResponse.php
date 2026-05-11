<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\LaravelData\Data;

/**
 * @template TContent
 */
class ApiResponse extends Data implements Responsable
{
    /**
     * @param int $Code Código HTTP
     * @param string $Message Mensaje descriptivo
     * @param TContent|null $Content Cuerpo de la respuesta tipado
     */
    public function __construct(
        public int $Code,
        public string $Message,
        public mixed $Content = null,
    ) {}

    /**
     * Crea la respuesta HTTP con el código de estado correcto.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json($this, $this->Code);
    }

    /**
     * Helper para respuestas exitosas.
     *
     * @template TSuccess
     * @param TSuccess|null $Content
     * @param string $Message
     * @param int $Code
     * @return self<TSuccess>
     */
    public static function success(mixed $Content = null, string $Message = 'Operación exitosa', int $Code = 200): self
    {
        return new self(
            Code: $Code,
            Message: $Message,
            Content: $Content
        );
    }

    /**
     * Helper para respuestas de error.
     *
     * @param string $Message
     * @param int $Code
     * @param mixed|null $Content
     * @return self<mixed>
     */
    public static function error(string $Message = 'Ha ocurrido un error', int $Code = 400, mixed $Content = null): self
    {
        return new self(
            Code: $Code,
            Message: $Message,
            Content: $Content
        );
    }
}
