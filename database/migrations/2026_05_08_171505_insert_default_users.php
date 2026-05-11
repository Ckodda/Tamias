<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $password = Hash::make('Tamias2024!');
        $now = now();

        // 1. Insertar Usuarios
        $superAdminId = DB::table('Users')->insertGetId([
            'FullName' => 'Super Administrator',
            'Email' => 'superadmin@tamias.com',
            'Password' => $password,
            'IsActive' => true,
            'CreatedAt' => $now,
            'UpdatedAt' => $now,
        ], 'Id');

        $adminId = DB::table('Users')->insertGetId([
            'FullName' => 'Admin User',
            'Email' => 'admin@tamias.com',
            'Password' => $password,
            'IsActive' => true,
            'CreatedAt' => $now,
            'UpdatedAt' => $now,
        ], 'Id');

        $viewerId = DB::table('Users')->insertGetId([
            'FullName' => 'Viewer User',
            'Email' => 'viewer@tamias.com',
            'Password' => $password,
            'IsActive' => true,
            'CreatedAt' => $now,
            'UpdatedAt' => $now,
        ], 'Id');

        // 2. Obtener IDs de Roles
        $roles = DB::table('Roles')->whereIn('name', ['SuperAdmin', 'Admin', 'Viewer'])->pluck('Id', 'name');

        // 3. Asignar Roles (Tabla ModelHasRoles según config/permission.php)
        DB::table('ModelHasRoles')->insert([
            [
                'RoleId' => $roles['SuperAdmin'],
                'ModelId' => $superAdminId,
                'model_type' => 'App\Models\User',
            ],
            [
                'RoleId' => $roles['Admin'],
                'ModelId' => $adminId,
                'model_type' => 'App\Models\User',
            ],
            [
                'RoleId' => $roles['Viewer'],
                'ModelId' => $viewerId,
                'model_type' => 'App\Models\User',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $emails = ['superadmin@tamias.com', 'admin@tamias.com', 'viewer@tamias.com'];
        $userIds = DB::table('Users')->whereIn('Email', $emails)->pluck('Id');

        DB::table('ModelHasRoles')->whereIn('ModelId', $userIds)->where('model_type', 'App\Models\User')->delete();
        DB::table('Users')->whereIn('Id', $userIds)->delete();
    }
};
