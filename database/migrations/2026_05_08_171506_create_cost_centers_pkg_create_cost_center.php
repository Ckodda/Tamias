<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path('scripts/CostCentersPkg/CreateCostCenter.sql');
        if (File::exists($path)) {
            // Usamos REPLACE en el SQL, así que simplemente lo volvemos a ejecutar
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "CostCentersPkg"."CreateCostCenter"(VARCHAR, REFCURSOR)');
    }
};
