<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // ID de usuario fijo solicitado

        $methods = [
            ['MethodName' => 'Efectivo'],
            ['MethodName' => 'Transferencia Bancaria'],
            ['MethodName' => 'Tarjeta de Crédito'],
            ['MethodName' => 'Tarjeta de Débito'],
            ['MethodName' => 'Yape / Plin'],
            ['MethodName' => 'Depósito en cuenta'],
        ];

        foreach ($methods as $method) {
            DB::table('PaymentMethods')->updateOrInsert(
                ['MethodName' => $method['MethodName']], // Criterio de búsqueda
                [
                    'IsActive'  => true,
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ]
            );
        }
    }
}
