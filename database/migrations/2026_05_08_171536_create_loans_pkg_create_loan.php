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
        // Asegurar que el esquema existe
        DB::statement('CREATE SCHEMA IF NOT EXISTS "LoansPkg"');

        $path = database_path('scripts/LoansPkg/CreateLoan.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La firma del procedimiento debe coincidir con la de creación
        DB::statement('DROP PROCEDURE IF EXISTS "LoansPkg"."CreateLoan"(VARCHAR, DECIMAL, DECIMAL, DECIMAL, DATE, VARCHAR, BOOLEAN, BIGINT, BIGINT, BIGINT, REFCURSOR)');
    }
};
