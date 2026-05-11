<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            [
                'name' => 'SuperAdmin',
                'guard_name' => 'api',
                'CreatedAt' => now(),
                'UpdatedAt' => now(),
            ],
            [
                'name' => 'Admin',
                'guard_name' => 'api',
                'CreatedAt' => now(),
                'UpdatedAt' => now(),
            ],
            [
                'name' => 'Viewer',
                'guard_name' => 'api',
                'CreatedAt' => now(),
                'UpdatedAt' => now(),
            ],
        ];

        DB::table('Roles')->insert($roles);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('Roles')->whereIn('name', ['SuperAdmin', 'Admin', 'Viewer'])->delete();
    }
};
