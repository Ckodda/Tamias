<?php

namespace Tests\Feature\Actions\MonthlyBalance;

use App\Models\CostCenter;
use App\Models\MonthlyBalance;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MonthlyBalanceFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Obtención del listado paginado de balances mensuales.
     * Verifica que la estructura ApiResponse y PaginatedResponse sea correcta.
     */
    public function test_its_list_monthly_balances_with_paginated_structure(): void
    {
        // --- PREPARACIÓN ---
        $queryParams = [
            'PageSize'   => 12,
            'PageNumber' => 1
        ];

        // --- EJECUCIÓN ---
        $response = $this->withToken($this->apiToken)
            ->getJson('/api/monthly-balances/?' . http_build_query($queryParams));
        // --- ASERCIONES ---
        $response->assertStatus(200)
            ->assertJson([
                'Code' => 200,
                'Message' => 'Listado obtenido correctamente'
            ]);

        // Validación de estructura PascalCase definida en tus DTOs
        $response->assertJsonStructure([
            'Code',
            'Message',
            'Content' => [
                'Items' => [
                    '*' => [
                        'Id',
                        'MonthPeriod',
                        'TotalIncomes',
                        'TotalExpenses',
                        'CostCenterId',
                        'CenterName',
                        'ClosingBalance'
                    ]
                ],
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ]
        ]);

        // Verificamos que existan datos (asumiendo que el seeder pobló la tabla)
        $this->assertNotEmpty($response->json('Content.Items'));
    }

    /**
     * Test: Filtro por Centro de Costo y rango de fechas.
     */
    public function test_its_filters_monthly_balances_by_cost_center_and_date(): void
    {
        // Obtenemos un ID válido del seeder
        $costCenter = CostCenter::first();

        $queryParams = [
            'CostCenterId' => $costCenter->Id,
            'StartMonth'   => now()->startOfYear()->format('Y-m-d'),
            'EndMonth'     => now()->endOfYear()->format('Y-m-d'),
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/monthly-balances/?' . http_build_query($queryParams));

        $response->assertStatus(200);

        // Validamos que los items devueltos pertenezcan al Centro de Costo filtrado
        $items = $response->json('Content.Items');
        foreach ($items as $item) {
            $this->assertEquals($costCenter->Id, $item['CostCenterId']);
        }
    }

    /**
     * Test: Validación de formato de fecha incorrecto.
     */
    public function test_its_fails_on_invalid_date_format(): void
    {
        $queryParams = [
            'StartMonth' => '2024-13-01', // Mes inválido
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/monthly-balances/?' . http_build_query($queryParams));

        // Laravel Data debería disparar error de validación 422
        $response->assertStatus(422);
    }
}
