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
        $path = database_path('scripts/CurrenciesPkg/CreateCurrency.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Actualizado para coincidir con la nueva firma: Name, Code, Symbol, Rate, CreatedBy, ResultSet
        DB::statement('DROP PROCEDURE IF EXISTS "CurrenciesPkg"."CreateCurrency"(VARCHAR, VARCHAR, VARCHAR, DECIMAL, BIGINT, REFCURSOR)');
    }
};
