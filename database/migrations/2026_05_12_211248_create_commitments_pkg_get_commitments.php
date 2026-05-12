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
        $path = database_path('scripts/CommitmentsPkg/GetCommitments.sql');

        if (File::exists($path)) {
            // Se ejecuta el script que crea el esquema y el procedimiento
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "CommitmentsPkg"."GetCommitments"(BIGINT, BIGINT, BIGINT, BIGINT, VARCHAR, INTEGER, INTEGER, REFCURSOR)');
    }
};
