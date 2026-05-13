<?php

namespace Tests\Feature\Actions\Event;

use App\Models\CostCenter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización de un Evento.
     * Valida la existencia previa de Centros de Costo.
     */
    public function test_its_create_and_update_event_with_dependency_check(): void
    {
        // --- VALIDACIÓN PREVENTIVA ---
        $latestCostCenter = CostCenter::latest('Id')->first();

        $this->assertNotNull(
            $latestCostCenter,
            'Abortando test: No existen registros en la tabla CostCenters. Es necesario un Centro de Costo para vincular el Evento.'
        );

        $latestCurrencyId = DB::table('Currencies')->latest('Id')->value('Id') ?? 1;

        // --- PASO 1: CREACIÓN (POST) ---
        $originalName = 'EVENTO DE GALA ' . fake()->unique()->bothify('####');

        $createData = [
            'CostCenterId' => $latestCostCenter->Id,
            'CurrencyId' => $latestCurrencyId,
            'EventName' => $originalName,
            'TargetAmount' => 10000.00,
            'EventStatus' => 'Active',
            'StartDate' => now()->format('Y-m-d'),
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/events/', $createData);

        $createResponse->dump();
        $createResponse->assertStatus(201);
        $createdId = $createResponse->json('Content.Id');

        // --- PASO 2: ACTUALIZACIÓN (PUT) ---
        $updatedName = 'EVENTO ACTUALIZADO ' . fake()->unique()->word();

        $updateData = [
            'Id' => $createdId,
            'EventName' => $updatedName,
            'EventStatus' => 'Completed',
            'IsActive' => true
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/events/', $updateData);

        $updateResponse->assertStatus(200);

        $this->assertDatabaseHas('Events', [
            'Id' => $createdId,
            'EventName' => $updatedName
        ]);
    }

    /**
     * Test: Obtención del listado de eventos con el formato PaginatedResponse correcto.
     */
    public function test_its_list_events_with_paginated_response_structure(): void
    {
        $queryParams = [
            'PageSize' => 10,
            'PageNumber' => 1
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/events/?' . http_build_query($queryParams));

        $response->assertStatus(200);

        // Ajustado a las propiedades de tu clase PaginatedResponse (Mayúsculas y sin anidación meta)
        $response->assertJsonStructure([
            'Content' => [
                'Items',
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ],
            'Message',
            'Code'
        ]);

        // Verificación opcional de tipos de datos en la respuesta
        $this->assertIsArray($response->json('Content.Items'));
        $this->assertIsInt($response->json('Content.TotalCount'));
    }
}
