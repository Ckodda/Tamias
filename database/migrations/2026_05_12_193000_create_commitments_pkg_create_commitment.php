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
        DB::statement('CREATE SCHEMA IF NOT EXISTS "CommitmentsPkg"');

        $path = database_path('scripts/CommitmentsPkg/CreateCommitment.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP PROCEDURE IF EXISTS "CommitmentsPkg"."CreateCommitment"(BIGINT, BIGINT, BIGINT, DECIMAL, VARCHAR, VARCHAR, BIGINT, REFCURSOR)');
    }
};
