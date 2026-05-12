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
        DB::unprepared('CREATE SCHEMA IF NOT EXISTS "LoansPkg";');

        $path = database_path('scripts/LoansPkg/GetLoans.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS "LoansPkg"."GetLoans"(BIGINT, VARCHAR, BIGINT, DATE, BOOLEAN, INT, INT);');

    }
};
