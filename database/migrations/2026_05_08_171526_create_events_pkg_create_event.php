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
        DB::statement('CREATE SCHEMA IF NOT EXISTS "EventsPkg"');

        $path = database_path('scripts/EventsPkg/CreateEvent.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Firma actualizada con BIGINT extra para CurrencyId
        DB::statement('DROP PROCEDURE IF EXISTS "EventsPkg"."CreateEvent"(BIGINT, BIGINT, VARCHAR, DECIMAL, VARCHAR, DATE, BIGINT, REFCURSOR)');
    }
};
