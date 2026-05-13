<?php

namespace Tests\Feature\Actions\PaymentMethod;

use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PaymentMethodFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización de un Método de Pago.
     * Valida la estructura ApiResponse y la persistencia en base de datos.
     */
    public function test_its_create_and_update_payment_method_correctly(): void
    {
        // --- PASO 1: CREACIÓN (POST) ---
        $originalName = 'METODO TEST ' . fake()->unique()->word();

        $createData = [
            'MethodName' => $originalName,
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/payment-methods/', $createData);

        // Validamos estructura ApiResponse (PascalCase)
        $createResponse->assertStatus(201)
            ->assertJson([
                'Code' => 201,
                'Message' => 'Metodo de pago registrado exitosamente'
            ]);

        // --- PASO 2: RECUPERAR EL ÚLTIMO REGISTRO ---
        $latestMethod = PaymentMethod::latest('Id')->first();

        $this->assertNotNull(
            $latestMethod,
            'No se encontró el registro en la base de datos tras la creación.'
        );

        // --- PASO 3: ACTUALIZACIÓN (PUT) ---
        $updatedName = 'METODO ACTUALIZADO ' . fake()->unique()->word();

        $updateData = [
            'Id' => $latestMethod->Id,
            'MethodName' => $updatedName,
            'IsActive' => false
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/payment-methods/', $updateData);

        // Validamos estructura ApiResponse en actualización
        $updateResponse->assertStatus(200)
            ->assertJson([
                'Code' => 200,
                'Message' => 'Metodo de pago actualizado exitosamente'
            ]);

        // Verificación final en base de datos de PostgreSQL
        $this->assertDatabaseHas('PaymentMethods', [
            'Id' => $latestMethod->Id,
            'MethodName' => $updatedName,
            'IsActive' => false
        ]);
    }

    /**
     * Test: Obtención del listado con PaginatedResponse anidado en ApiResponse.
     */
    public function test_its_list_payment_methods_with_paginated_structure(): void
    {
        // Aseguramos existencia de datos (usando el usuario 1 de tus seeders)
        PaymentMethod::firstOrCreate(
            ['MethodName' => 'Efectivo Sistema'],
            ['IsActive' => true, 'CreatedBy' => 1]
        );

        $queryParams = [
            'PageSize' => 5,
            'PageNumber' => 1
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/payment-methods/?' . http_build_query($queryParams));

        $response->assertStatus(200);

        // Validación de estructura completa: ApiResponse + PaginatedResponse
        $response->assertJsonStructure([
            'Code',
            'Message',
            'Content' => [
                'Items',
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ]
        ]);

        // Verificación de que el código en el JSON coincide con el HTTP Status
        $this->assertEquals(200, $response->json('Code'));
    }

    /**
     * Test: Validación de errores (422) con estructura ApiResponse.
     */
    public function test_its_fails_creation_with_invalid_data(): void
    {
        $invalidData = [
            'MethodName' => 'Ab', // Falla Min(3) según tu Request
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/payment-methods/', $invalidData);

        $response->assertStatus(422)
            ->assertJson([
                'Code' => 422,
                'Message' => 'Los datos proporcionados no son válidos.'
            ]);

        // Validamos que los errores de validación vengan dentro de 'Content'
        $response->assertJsonStructure([
            'Content' => [
                'MethodName'
            ]
        ]);
    }
}
