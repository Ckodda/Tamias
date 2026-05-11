<?php

namespace App\Http\Controllers\Api;

use App\Actions\Event\CreateEventAction;
use App\Actions\Event\GetEventsAction;
use App\Actions\Event\UpdateEventAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\GetEventsRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Responses\Event\EventResponse;
use App\Http\Responses\PaginatedResponse;
use Exception;
use Illuminate\Validation\ValidationException;

class EventController extends Controller
{
    /**
     * Listado paginado de Eventos.
     *
     * @param GetEventsRequest $request
     * @param GetEventsAction $action
     * @return ApiResponse<PaginatedResponse>
     */
    public function index(GetEventsRequest $request, GetEventsAction $action): ApiResponse
    {
        try {
            $results = $action->execute($request);

            return ApiResponse::success(
                Content: $results,
                Message: 'Listado de eventos obtenido correctamente'
            );

        } catch (Exception $exception) {
            return ApiResponse::error(
                Message: $exception->getMessage(),
                Code: 500
            );
        }
    }

    /**
     * Registra un nuevo Evento.
     *
     * @param CreateEventRequest $request
     * @param CreateEventAction $action
     * @return ApiResponse<EventResponse>
     */
    public function store(CreateEventRequest $request, CreateEventAction $action): ApiResponse
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
     * Actualiza un Evento existente.
     *
     * @param UpdateEventRequest $request
     * @param UpdateEventAction $action
     * @return ApiResponse<EventResponse>
     */
    public function update(UpdateEventRequest $request, UpdateEventAction $action): ApiResponse
    {
        try {
            $response = $action->execute($request);

            return ApiResponse::success(
                Content: $response,
                Message: 'Evento actualizado exitosamente'
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
