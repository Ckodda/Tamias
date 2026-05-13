<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // ID de usuario fijo solicitado

        // Recuperamos IDs existentes para mantener la integridad referencial
        $costCenterId = DB::table('CostCenters')->first()->Id;
        $solesCurrencyId = DB::table('Currencies')->where('CurrencyCode', 'PEN')->value('Id');
        $dollarsCurrencyId = DB::table('Currencies')->where('CurrencyCode', 'USD')->value('Id');

        $events = [
            [
                'CostCenterId' => $costCenterId,
                'EventName'    => 'Campaña Escolar 2026',
                'TargetAmount' => 15000.00,
                'EventStatus'  => 'Active',
                'StartDate'    => '2026-02-01',
                'CurrencyId'   => $solesCurrencyId,
                'IsActive'     => true,
            ],
            [
                'CostCenterId' => $costCenterId,
                'EventName'    => 'Renovación de Equipos TI',
                'TargetAmount' => 5000.00,
                'EventStatus'  => 'Active',
                'StartDate'    => '2026-03-15',
                'CurrencyId'   => $dollarsCurrencyId,
                'IsActive'     => true,
            ],
            [
                'CostCenterId' => $costCenterId,
                'EventName'    => 'Evento Corporativo Anual',
                'TargetAmount' => 25000.00,
                'EventStatus'  => 'Completed',
                'StartDate'    => '2025-12-20',
                'CurrencyId'   => $solesCurrencyId,
                'IsActive'     => true,
            ],
        ];

        foreach ($events as $event) {
            DB::table('Events')->insert(
                array_merge($event, [
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ])
            );
        }
    }
}
