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
        $path = database_path('scripts/CurrenciesPkg/GetCurrencies.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "CurrenciesPkg"."GetCurrencies"(VARCHAR, VARCHAR, BOOLEAN, INTEGER, INTEGER, REFCURSOR)');
    }
};
