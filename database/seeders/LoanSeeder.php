<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // ID de usuario fijo solicitado

        // Recuperamos los IDs de monedas existentes
        $penCurrencyId = DB::table('Currencies')->where('CurrencyCode', 'PEN')->value('Id');
        $usdCurrencyId = DB::table('Currencies')->where('CurrencyCode', 'USD')->value('Id');

        $loans = [
            [
                'LenderName'       => 'Banco de Crédito del Perú (BCP)',
                'PrincipalAmount'  => 5000.00,
                'InterestAmount'   => 250.00,
                'TotalToRepay'     => 5250.00,
                'RepaymentDueDate' => '2026-06-15',
                'LoanStatus'       => 'Pending',
                'CurrencyId'       => $penCurrencyId,
                'IsActive'         => true,
            ],
            [
                'LenderName'       => 'Caja Metropolitana',
                'PrincipalAmount'  => 1500.00,
                'InterestAmount'   => 75.00,
                'TotalToRepay'     => 1575.00,
                'RepaymentDueDate' => '2026-07-20',
                'LoanStatus'       => 'Pending',
                'CurrencyId'       => $penCurrencyId,
                'IsActive'         => true,
            ],
            [
                'LenderName'       => 'Préstamo Directo - Socio 04',
                'PrincipalAmount'  => 1000.00,
                'InterestAmount'   => 0.00,
                'TotalToRepay'     => 1000.00,
                'RepaymentDueDate' => '2026-01-10',
                'LoanStatus'       => 'Paid',
                'CurrencyId'       => $usdCurrencyId,
                'IsActive'         => true,
            ],
        ];

        foreach ($loans as $loan) {
            DB::table('Loans')->insert(
                array_merge($loan, [
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ])
            );
        }
    }
}
