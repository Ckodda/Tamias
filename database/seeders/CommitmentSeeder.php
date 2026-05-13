<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommitmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // Usuario principal solicitado

        // Recuperamos referencias existentes
        $costCenter = DB::table('CostCenters')->first();
        $event = DB::table('Events')->first();

        if (!$costCenter || !$event) {
            $this->command->error('Faltan dependencias: Asegúrate de correr CostCenterSeeder y EventSeeder primero.');
            return;
        }

        $commitments = [
            [
                'UserId'           => $userId,
                'CostCenterId'     => $costCenter->Id,
                'EventId'          => null, // Compromiso general con el Centro de Costo
                'CommitmentAmount' => 100.00,
                'FrequencyType'    => 'Monthly',
                'CurrentStatus'    => 'Active',
                'IsActive'         => true,
            ],
            [
                'UserId'           => $userId,
                'CostCenterId'     => $costCenter->Id,
                'EventId'          => $event->Id, // Compromiso vinculado a un evento específico
                'CommitmentAmount' => 500.00,
                'FrequencyType'    => 'OneTime',
                'CurrentStatus'    => 'Active',
                'IsActive'         => true,
            ],
            [
                'UserId'           => $userId,
                'CostCenterId'     => $costCenter->Id,
                'EventId'          => null,
                'CommitmentAmount' => 150.00,
                'FrequencyType'    => 'Monthly',
                'CurrentStatus'    => 'Fulfilled',
                'IsActive'         => true,
            ],
            [
                'UserId'           => $userId,
                'CostCenterId'     => $costCenter->Id,
                'EventId'          => $event->Id,
                'CommitmentAmount' => 200.00,
                'FrequencyType'    => 'OneTime',
                'CurrentStatus'    => 'Cancelled',
                'IsActive'         => false,
            ],
        ];

        foreach ($commitments as $commitment) {
            DB::table('Commitments')->insert(
                array_merge($commitment, [
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ])
            );
        }
    }
}
