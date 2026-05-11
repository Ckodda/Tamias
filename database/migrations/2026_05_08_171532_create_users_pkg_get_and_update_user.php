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
        DB::unprepared('CREATE SCHEMA IF NOT EXISTS "UsersPkg";');

        $path = database_path('scripts/UsersPkg/GetUsers.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }

        $path = database_path('scripts/UsersPkg/UpdateUser.sql');
        if (File::exists($path)) {
            DB::unprepared(File::get($path));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS "UsersPkg"."GetUsers"(BIGINT, VARCHAR, VARCHAR, BOOLEAN, INT, INT);');
        DB::unprepared('DROP PROCEDURE IF EXISTS "UsersPkg"."UpdateUser"(BIGINT, VARCHAR, VARCHAR, VARCHAR, BOOLEAN, BIGINT);');
    }
};
