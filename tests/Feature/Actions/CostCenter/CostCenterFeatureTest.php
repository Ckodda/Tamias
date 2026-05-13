<?php

namespace Tests\Feature\Actions\CostCenter;

use App\Models\CostCenter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CostCenterFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización de un Centro de Costo.
     * Recupera el último registro de la base de datos para realizar la actualización.
     */
    public function test_its_create_and_update_cost_center_using_latest_record(): void
    {
        // --- PASO 1: CREACIÓN (POST) ---
        $originalCode = 'CC-' . fake()->unique()->bothify('####');
        $originalName = 'CENTRO DE PRUEBA ' . fake()->unique()->word();

        $createData = [
            'CodeCostCenter' => $originalCode,
            'CenterName' => $originalName,
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/cost-centers/', $createData);

        $createResponse->assertStatus(201);
        $createResponse->assertJsonPath('Message', 'Centro de Costo creado exitosamente');

        // --- PASO 2: RECUPERAR EL ÚLTIMO REGISTRO ---
        $latestCostCenter = CostCenter::latest('Id')->first();

        $this->assertNotNull(
            $latestCostCenter,
            'El registro no fue encontrado en la base de datos tras la creación.'
        );

        // --- PASO 3: ACTUALIZACIÓN (PUT) ---
        $updatedCode = 'CC-UPD-' . fake()->unique()->bothify('####');
        $updatedName = 'CENTRO ACTUALIZADO ' . fake()->unique()->word();

        $updateData = [
            'Id' => $latestCostCenter->Id,
            'CodeCostCenter' => $updatedCode,
            'CenterName' => $updatedName,
            'IsActive' => false
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/cost-centers/', $updateData);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('Message', 'Centro de Costo actualizado exitosamente');

        // Verificación en base de datos
        $this->assertDatabaseHas('CostCenters', [
            'Id' => $latestCostCenter->Id,
            'CodeCostCenter' => $updatedCode,
            'CenterName' => $updatedName,
            'IsActive' => false
        ]);
    }

    /**
     * Test: Obtención del listado de Centros de Costo con PaginatedResponse.
     */
    public function test_its_list_cost_centers_with_correct_paginated_structure(): void
    {
        // Aseguramos que exista al menos un registro para que la prueba sea significativa
        CostCenter::updateOrCreate(
            ['Id' => 1],
            ['CodeCostCenter' => 'CC-001', 'CenterName' => 'Centro Base', 'IsActive' => true]
        );

        $queryParams = [
            'PageSize' => 5,
            'PageNumber' => 1,
            'IsActive' => true
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/cost-centers/?' . http_build_query($queryParams));

        $response->assertStatus(200);
        $response->assertJsonPath('Message', 'Listado obtenido correctamente');

        // Validación de la estructura PaginatedResponse (PascalCase)
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

        // Verificación de integridad de datos en el primer elemento
        if (count($response->json('Content.Items')) > 0) {
            $response->assertJsonStructure([
                'Content' => [
                    'Items' => [
                        '*' => [
                            'Id',
                            'CodeCostCenter',
                            'CenterName',
                            'IsActive'
                        ]
                    ]
                ]
            ]);
        }
    }

    /**
     * Test: Validación de error al crear (Data incompleta).
     */
    public function test_its_fails_creation_on_validation_error(): void
    {
        $invalidData = [
            'CodeCostCenter' => '12', // Falla Min(3)
            'CenterName' => ''        // Falla Required
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/cost-centers/', $invalidData);

        $response->assertStatus(422);
        $response->assertJsonPath('Message', 'Los datos proporcionados no son válidos.');
        $response->assertJsonStructure(['Content' => ['CodeCostCenter', 'CenterName']]);
    }
}
