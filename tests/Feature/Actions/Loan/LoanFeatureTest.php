<?php

namespace Tests\Feature\Actions\Loan;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoanFeatureTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test para la creación de un nuevo préstamo.
     */
    public function test_its_create_loan_process_correctly(): void
    {
        $data = [
            'LenderName' => 'Prestamista de Prueba Unitario',
            'PrincipalAmount' => 1500.50,
            'InterestAmount' => 50.00,
            'TotalToRepay' => 1550.50,
            'RepaymentDueDate' => '2026-12-31',
            'CurrencyId' => 1,
            'LoanStatus' => 'Pending'
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/loans/', $data);

        // Validamos status 201 según tu controlador
        $response->assertStatus(201);

        // Verificamos estructura básica de tu ApiResponse
        $response->assertJsonPath('Message', 'Evento registrado exitosamente');

        // Verificamos persistencia
        $this->assertDatabaseHas('Loans', [
            'LenderName' => 'Prestamista de Prueba Unitario',
            'PrincipalAmount' => 1500.50,
            'LoanStatus' => 'Pending'
        ]);
    }

    /**
     * Test para el listado paginado de préstamos.
     */
    public function test_its_list_loans_correctly(): void
    {
        // Filtros según GetLoansRequest
        $queryParams = [
            'PageSize' => 5,
            'PageNumber' => 1,
            'LoanStatus' => 'Pending'
        ];

        $response = $this->withToken($this->apiToken)
            ->getJson('/api/loans/?' . http_build_query($queryParams));

        $response->assertStatus(201); // Tu controlador devuelve 201 en index

        $response->assertJsonStructure([
            'Content',
            'Message',
            'Code'
        ]);
    }

    /**
     * Test de validación al crear (caso de error).
     */
    public function test_its_fails_creation_when_required_fields_are_missing(): void
    {
        $data = [
            'LenderName' => '', // Requerido en CreateLoanRequest
        ];

        $response = $this->withToken($this->apiToken)
            ->postJson('/api/loans/', $data);

        // Tu controlador captura ValidationException y devuelve 422
        $response->assertStatus(422);
        $response->assertJsonPath('Message', 'Los datos proporcionados no son válidos.');
    }
}
