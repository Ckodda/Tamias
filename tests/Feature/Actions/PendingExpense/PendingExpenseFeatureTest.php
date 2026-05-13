<?php

namespace Tests\Feature\Actions\PendingExpense;

use App\Models\CostCenter;
use App\Models\PendingExpense;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PendingExpenseFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización.
     * Aprovecha los Centros de Costo creados por el CostCenterSeeder.
     */
    public function test_its_create_and_update_pending_expense_using_seeded_data(): void
    {
        // --- PREPARACIÓN ---
        // Recuperamos el primer Centro de Costo del seeder
        $costCenter = CostCenter::first();

        // --- PASO 1: CREACIÓN (POST) ---
        $createData = [
            'CostCenterId' => $costCenter->Id,
            'ExpenseDescription' => 'Renovación de Licencias JetBrains',
            'TotalAmount' => 499.00,
            'DueDate' => now()->addMonth()->format('Y-m-d'),
            'ProviderName' => 'JetBrains s.r.o.',
            'PaymentStatus' => 'Pending'
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/pending-expenses/', $createData);
        // Verificación ApiResponse (PascalCase)
        $createResponse->assertStatus(201)
            ->assertJson([
                'Code' => 201,
                'Message' => 'PendingExpense creado exitosamente'
            ]);

        // --- PASO 2: RECUPERAR EL REGISTRO RECIÉN CREADO ---
        $latestExpense = PendingExpense::latest('Id')->first();

        // --- PASO 3: ACTUALIZACIÓN (PUT) ---
        $updateData = [
            'Id' => $latestExpense->Id,
            'PaymentStatus' => 'Paid',
            'IsActive' => true
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/pending-expenses/', $updateData);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'Code' => 200,
                'Message' => 'PendingExpense actualizado exitosamente'
            ]);

        // Verificación de persistencia en la tabla public.PendingExpenses
        $this->assertDatabaseHas('PendingExpenses', [
            'Id' => $latestExpense->Id,
            'PaymentStatus' => 'Paid',
            'UpdatedBy' => 1 // Asumido por el middleware/auth del seeder
        ]);
    }

    /**
     * Test: Listado paginado de gastos pendientes registrados por el seeder.
     */
    public function test_its_list_seeded_pending_expenses(): void
    {
        // Asumimos que PendingExpenseSeeder insertó al menos un registro
        $response = $this->withToken($this->apiToken)
            ->getJson('/api/pending-expenses/?PageSize=10&PageNumber=1');

        $response->assertStatus(200);

        // Validación de estructura ApiResponse + PaginatedResponse
        $response->assertJsonStructure([
            'Code',
            'Message',
            'Content' => [
                'Items' => [
                    '*' => [
                        'Id',
                        'CostCenterId',
                        'ExpenseDescription',
                        'TotalAmount',
                        'DueDate',
                        'ProviderName',
                        'PaymentStatus'
                    ]
                ],
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ]
        ]);

        // Verificamos que el contenido no sea nulo ya que el seeder fue ejecutado
        $this->assertNotEmpty($response->json('Content.Items'));
    }

    /**
     * Test: Validación de filtros sobre datos existentes.
     */
    public function test_its_filters_pending_expenses_by_status(): void
    {
        $response = $this->withToken($this->apiToken)
            ->getJson('/api/pending-expenses/?PaymentStatus=Paid');

        $response->assertStatus(200);

        // Validamos que todos los items devueltos tengan el status filtrado
        foreach ($response->json('Content.Items') as $item) {
            $this->assertEquals('Paid', $item['PaymentStatus']);
        }
    }

    /**
     * Test: Fallo de validación al intentar crear con un CostCenterId inexistente.
     */
    public function test_its_fails_with_non_existent_cost_center(): void
    {
        $invalidData = [
            'CostCenterId' => 999999, // ID que no existe en la BD
            'ExpenseDescription' => 'Gasto Inválido',
            'TotalAmount' => 10.00,
            'DueDate' => '2026-12-31',
            'ProviderName' => 'Desconocido',
            'PaymentStatus' => 'Pending'
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/pending-expenses/', $invalidData);

        // Si tu Action/Controller tiene validación de existencia (exists:CostCenters,Id)
        $response->assertStatus(422);
    }
}
