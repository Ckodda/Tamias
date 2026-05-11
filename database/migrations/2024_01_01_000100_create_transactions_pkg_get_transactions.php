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
        $path = database_path('scripts/TransactionsPkg/GetTransactions.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cambiado de FUNCTION a PROCEDURE
        DB::statement('DROP PROCEDURE IF EXISTS "TransactionsPkg"."GetTransactions"(DATE, DATE, BIGINT, BIGINT, BIGINT, BIGINT, VARCHAR, REFCURSOR)');
    }
};
