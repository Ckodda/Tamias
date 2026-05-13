<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CostCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = 1; // ID de usuario fijo solicitado

        $costCenters = [
            [
                'CodeCostCenter' => 'ADM-001',
                'CenterName'     => 'Administración Central',
                'IsActive'       => true,
            ],
            [
                'CodeCostCenter' => 'VEN-002',
                'CenterName'     => 'Departamento de Ventas',
                'IsActive'       => true,
            ],
            [
                'CodeCostCenter' => 'OPE-003',
                'CenterName'     => 'Operaciones y Logística',
                'IsActive'       => true,
            ],
            [
                'CodeCostCenter' => 'TI-004',
                'CenterName'     => 'Tecnología de Información',
                'IsActive'       => true,
            ],
            [
                'CodeCostCenter' => 'RRHH-005',
                'CenterName'     => 'Recursos Humanos',
                'IsActive'       => true,
            ],
        ];

        foreach ($costCenters as $center) {
            DB::table('CostCenters')->updateOrInsert(
                ['CodeCostCenter' => $center['CodeCostCenter']], // Clave única
                array_merge($center, [
                    'CreatedBy' => $userId,
                    'UpdatedBy' => $userId,
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                ])
            );
        }
    }
}
