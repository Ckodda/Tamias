<?php

namespace Tests\Feature\Actions\Commitment;

use App\Models\Commitment;
use App\Models\CostCenter;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CommitmentFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización de un compromiso.
     * Utiliza datos pre-existentes de los seeders.
     */
    public function test_its_create_and_update_commitment_correctly(): void
    {
        // --- PREPARACIÓN ---
        // Recuperamos datos del seeder (User 1, primer CostCenter y primer Event)
        $user = User::find(1);
        $costCenter = CostCenter::first();
        $event = Event::first();

        // --- PASO 1: CREACIÓN (POST) ---
        $createData = [
            'UserId' => $user->Id,
            'CostCenterId' => $costCenter->Id,
            'EventId' => $event->Id,
            'CommitmentAmount' => 250.00,
            'FrequencyType' => 'Monthly',
            'CurrentStatus' => 'Active'
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/commitments/', $createData);

        $createResponse->assertStatus(201)
            ->assertJson([
                'Code' => 201,
                'Message' => 'Commitment creado exitosamente'
            ]);

        // --- PASO 2: RECUPERACIÓN ---
        $latestCommitment = Commitment::latest('Id')->first();
        $this->assertNotNull($latestCommitment);

        // --- PASO 3: ACTUALIZACIÓN (PUT) ---
        $updateData = [
            'Id' => $latestCommitment->Id,
            'UserId' => $user->Id,
            'CostCenterId' => $costCenter->Id,
            'EventId' => $event->Id,
            'CommitmentAmount' => 300.00, // Incrementamos el monto
            'FrequencyType' => 'Monthly',
            'CurrentStatus' => 'Fulfilled' // Cambiamos estado
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/commitments/', $updateData);
        $updateResponse->assertStatus(200)
            ->assertJson([
                'Code' => 200,
                'Message' => 'Commitment actualizado exitosamente'
            ]);

        // Verificación en base de datos PostgreSQL
        $this->assertDatabaseHas('Commitments', [
            'Id' => $latestCommitment->Id,
            'CommitmentAmount' => 300.00,
            'CurrentStatus' => 'Fulfilled'
        ]);
    }

    /**
     * Test: Obtención del listado paginado de compromisos del seeder.
     */
    public function test_its_list_commitments_with_paginated_structure(): void
    {
        $queryParams = [
            'PageSize' => 5,
            'PageNumber' => 1
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/commitments/?' . http_build_query($queryParams));
        $response->assertStatus(200);

        // Validación de estructura ApiResponse + PaginatedResponse (PascalCase)
        $response->assertJsonStructure([
            'Code',
            'Message',
            'Content' => [
                'Items' => [
                    '*' => [
                        'Id',
                        'UserId',
                        'CostCenterId',
                        'EventId',
                        'CommitmentAmount',
                        'FrequencyType',
                        'CurrentStatus'
                    ]
                ],
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ]
        ]);

        // Verificamos que el contenido traiga los datos del seeder
        $this->assertNotEmpty($response->json('Content.Items'));
    }

    /**
     * Test: Validación de reglas de enumeración (FrequencyType inválido).
     */
    public function test_its_fails_on_invalid_frequency_type(): void
    {
        $user = User::find(1);
        $costCenter = CostCenter::first();
        $event = Event::first();

        $invalidData = [
            'UserId' => $user->Id,
            'CostCenterId' => $costCenter->Id,
            'EventId' => $event->Id,
            'CommitmentAmount' => 100,
            'FrequencyType' => 'Weekly', // No permitido en ['Monthly', 'OneTime']
            'CurrentStatus' => 'Active'
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/commitments/', $invalidData);

        $response->assertStatus(422)
            ->assertJson([
                'Code' => 422,
                'Message' => 'Los datos proporcionados no son válidos.'
            ]);

        $response->assertJsonValidationErrors(['FrequencyType']);
    }
}
