<?php

namespace Tests\Feature\Actions\Currency;

use App\Models\Currency;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CurrencyFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test: Flujo completo de creación y actualización de una Moneda.
     * Valida la persistencia en PostgreSQL y la estructura ApiResponse.
     */
    public function test_its_create_and_update_currency_correctly(): void
    {
        // --- PASO 1: CREACIÓN (POST) ---
        $createData = [
            'CurrencyName'   => 'Peso Mexicano',
            'CurrencyCode'   => 'MXN',
            'CurrencySymbol' => '$',
            'ExchangeRate'   => 0.054,
        ];

        $createResponse = $this->withToken($this->apiToken)
            ->postJson('/api/currencies/', $createData);

        // Verificamos respuesta exitosa 201 y estructura ApiResponse
        $createResponse->assertStatus(201)
            ->assertJson([
                'Code' => 201,
                'Message' => 'Moneda registrada exitosamente'
            ]);

        // --- PASO 2: RECUPERAR EL ÚLTIMO REGISTRO ---
        $latestCurrency = Currency::latest('Id')->first();

        $this->assertNotNull(
            $latestCurrency,
            'No se encontró la moneda en la base de datos tras el registro.'
        );

        // --- PASO 3: ACTUALIZACIÓN (PUT) ---
        $updatedData = [
            'Id'           => $latestCurrency->Id,
            'CurrencyName' => 'Peso Mexicano Actualizado',
            'ExchangeRate' => 0.055,
            'IsActive'     => false
        ];

        $updateResponse = $this->withToken($this->apiToken)
            ->putJson('/api/currencies/', $updatedData);

        $updateResponse->assertStatus(200)
            ->assertJson([
                'Code' => 200,
                'Message' => 'Moneda actualizada exitosamente'
            ]);

        // Verificación de persistencia real en PostgreSQL
        $this->assertDatabaseHas('Currencies', [
            'Id'           => $latestCurrency->Id,
            'CurrencyName' => 'Peso Mexicano Actualizado',
            'CurrencyCode' => 'MXN',
            'IsActive'     => false
        ]);
    }

    /**
     * Test: Obtención del listado paginado con estructura PaginatedResponse.
     */
    public function test_its_list_currencies_with_paginated_structure(): void
    {
        // Aseguramos que existan datos usando los registros de tus seeders previos
        if (Currency::count() === 0) {
            DB::table('Currencies')->insert([
                'CurrencyCode' => 'BRL',
                'CurrencyName' => 'Real Brasileño',
                'CurrencySymbol' => 'R$',
                'ExchangeRate' => 0.20,
                'CreatedBy' => 1,
                'CreatedAt' => now(),
                'UpdatedAt' => now()
            ]);
        }

        $queryParams = [
            'PageSize'   => 5,
            'PageNumber' => 1,
            'IsActive'   => true
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/currencies/?' . http_build_query($queryParams));

        $response->assertStatus(200);

        // Validación de estructura ApiResponse + PaginatedResponse (PascalCase)
        $response->assertJsonStructure([
            'Code',
            'Message',
            'Content' => [
                'Items' => [
                    '*' => [
                        'Id',
                        'CurrencyName',
                        'CurrencyCode',
                        'CurrencySymbol',
                        'ExchangeRate',
                        'IsActive'
                    ]
                ],
                'TotalCount',
                'PageNumber',
                'PageSize',
                'TotalPages'
            ]
        ]);
    }

    /**
     * Test: Validación de errores de campos obligatorios (422).
     */
    public function test_its_fails_creation_on_validation_error(): void
    {
        $invalidData = [
            'CurrencyName'   => 'MX', // Falla Min(3)
            'CurrencyCode'   => 'MEXICO', // Falla Max(3)
            'CurrencySymbol' => 'SYMBOL TOO LONG', // Falla Max(5)
            'ExchangeRate'   => -1 // Falla Min(0)
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/currencies/', $invalidData);

        $response->assertStatus(422)
            ->assertJson([
                'Code' => 422,
                'Message' => 'Los datos proporcionados no son válidos.'
            ]);

        // Verificamos que el Content contenga los errores de validación específicos
        $response->assertJsonStructure([
            'Content' => [
                'CurrencyName',
                'CurrencyCode',
                'CurrencySymbol',
                'ExchangeRate'
            ]
        ]);
    }
}
