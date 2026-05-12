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
        $path = database_path('scripts/TransactionsPkg/VoidTransaction.sql');

        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        } else {
            throw new \Exception("No se encontró el script SQL en: {$path}");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "TransactionsPkg"."VoidTransaction"(BIGINT, BIGINT, REFCURSOR)');
    }
};
