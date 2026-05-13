<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendingExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // ID de usuario fijo solicitado

        // Recuperamos el primer Centro de Costo para vincular los gastos
        $costCenter = DB::table('CostCenters')->first();

        if (!$costCenter) {
            $this->command->warn('No se encontraron Centros de Costo. Por favor, ejecuta CostCenterSeeder primero.');
            return;
        }

        $expenses = [
            [
                'CostCenterId'       => $costCenter->Id,
                'ExpenseDescription' => 'Servicio de Internet Fibra Óptica - Sede Central',
                'TotalAmount'        => 250.00,
                'DueDate'            => now()->addDays(15)->format('Y-m-d'),
                'ProviderName'       => 'Movistar Empresas',
                'PaymentStatus'      => 'Pending',
                'IsActive'           => true,
            ],
            [
                'CostCenterId'       => $costCenter->Id,
                'ExpenseDescription' => 'Mantenimiento preventivo de aire acondicionado',
                'TotalAmount'        => 450.50,
                'DueDate'            => now()->subDays(5)->format('Y-m-d'),
                'ProviderName'       => 'ClimaTotal S.A.C.',
                'PaymentStatus'      => 'Paid',
                'IsActive'           => true,
            ],
            [
                'CostCenterId'       => $costCenter->Id,
                'ExpenseDescription' => 'Compra de suministros de oficina (Papelería)',
                'TotalAmount'        => 120.00,
                'DueDate'            => now()->addDays(20)->format('Y-m-d'),
                'ProviderName'       => 'Tai Loy',
                'PaymentStatus'      => 'Pending',
                'IsActive'           => true,
            ],
            [
                'CostCenterId'       => $costCenter->Id,
                'ExpenseDescription' => 'Suscripción anual Software de Diseño',
                'TotalAmount'        => 1200.00,
                'DueDate'            => now()->subMonths(1)->format('Y-m-d'),
                'ProviderName'       => 'Adobe Systems',
                'PaymentStatus'      => 'Cancelled',
                'IsActive'           => true,
            ],
        ];

        foreach ($expenses as $expense) {
            DB::table('PendingExpenses')->insert(
                array_merge($expense, [
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ])
            );
        }
    }
}
