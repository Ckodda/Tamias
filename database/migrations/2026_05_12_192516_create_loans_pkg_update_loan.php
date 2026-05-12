<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('CREATE SCHEMA IF NOT EXISTS "LoansPkg";');

        $path = database_path('scripts/LoansPkg/UpdateLoan.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS "LoansPkg"."UpdateLoan"(BIGINT, VARCHAR, DECIMAL, DECIMAL, DECIMAL, DATE, VARCHAR, BOOLEAN, BIGINT, BIGINT);');
    }
};
