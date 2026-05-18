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
        $path = database_path('scripts/UsersPkg/GetUserRolesAndPermissions.sql');

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
        // Se elimina el procedimiento si se revierte la migración
        DB::statement('DROP PROCEDURE IF EXISTS "UsersPkg"."GetUserRolesAndPermissions"(BIGINT, REFCURSOR)');
    }
};