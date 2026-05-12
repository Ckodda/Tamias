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
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL3RhbWlhcy50ZXN0L2FwaS9hdXRoL2xvZ2luIiwiaWF0IjoxNzc4NjE1ODQwLCJleHAiOjE3Nzg2MTk0NDAsIm5iZiI6MTc3ODYxNTg0MCwianRpIjoiTVR4NGlQZ1R4NmlqVnpNRSIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.hz4NXj4xhHP_upPR8sqiSvP98-cqBtE-CXwe-2jZ5D8';
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
        $response = $this->withToken($token)
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
