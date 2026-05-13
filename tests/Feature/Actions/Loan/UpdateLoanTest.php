<?php

namespace Actions\Loan;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateLoanTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * A basic feature test example.
     */
    public function test_its_update_loan_process_correctly(): void
    {
        $data = [
            'Id'=>1,
            'LenderName'=>'Nueva Deuda de Usuario de Prueba',
            'PrincipalAmount'=>'200.00',
            'InterestAmount'=>'10.00',
            'TotalToRepay'=> '210.00',
            'RepaymentDueDate' => '2026-05-12',
            'CurrencyId' => 1,
            'LoanStatus' => 'Pending'
        ];
        $response = $this->withToken($this->apiToken)
            ->putJson('/api/loans/',$data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('Loans',[
            'Id'=>1,
            'LenderName'=>'Nueva Deuda de Usuario de Prueba',
            'PrincipalAmount'=>'200.00',
            'InterestAmount'=>'10.00',
            'TotalToRepay'=> '210.00',
            'RepaymentDueDate' => '2026-05-12',
            'CurrencyId' => 1,
            'LoanStatus' => 'Pending'
        ]);
    }
}
