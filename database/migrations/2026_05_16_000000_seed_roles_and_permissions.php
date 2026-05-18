<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Limpiar el cache de permisos de Spatie para evitar conflictos durante la migración
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definir Módulos y Acciones del sistema Tamias
        $modules = [
            'Commitment', 'CostCenter', 'Currency', 'Event', 'Loan',
            'MonthlyBalance', 'PaymentMethod', 'PendingExpense', 'Transaction', 'User'
        ];

        $actions = ['Create', 'Read', 'Update', 'Delete'];

        // 2. Crear todos los permisos combinados (Modulo:Accion)
        $allPermissionNames = [];
        foreach ($modules as $module) {
            foreach ($actions as $action) {
                $permissionName = "{$module}:{$action}";
                Permission::findOrCreate($permissionName, 'api');
                $allPermissionNames[] = $permissionName;
            }
        }

        // 3. Obtener Roles principales mediante DB para evitar conflictos de argumentos en el Query Builder
        // Usamos 'name' y 'guard_name' en minúsculas e 'Id' en PascalCase según tu estructura
        $roleTable = config('permission.table_names.roles');

        $superAdminId = DB::table($roleTable)->where('name', '=', 'SuperAdmin', 'and')->where('guard_name', '=', 'api', 'and')->value('Id');
        $adminId      = DB::table($roleTable)->where('name', '=', 'Admin', 'and')->where('guard_name', '=', 'api', 'and')->value('Id');
        $viewerId     = DB::table($roleTable)->where('name', '=', 'Viewer', 'and')->where('guard_name', '=', 'api', 'and')->value('Id');

        // Usamos findById que ahora referenciará a App\Models\Role y usará la columna 'Id' correctamente
        $superAdmin = $superAdminId ? Role::findById($superAdminId, 'api') : null;
        $admin      = $adminId ? Role::findById($adminId, 'api') : null;
        $viewer     = $viewerId ? Role::findById($viewerId, 'api') : null;

        // 4. Asignar Permisos a SuperAdmin (Todos los módulos, todas las acciones)
        $superAdmin?->syncPermissions($allPermissionNames);

        // 5. Asignar Permisos a Admin (Todos excepto el módulo de Usuarios)
        $adminPermissions = array_filter($allPermissionNames, function ($permission) {
            return !str_starts_with($permission, 'User:');
        });
        $admin?->syncPermissions($adminPermissions);

        // 6. Asignar Permisos a Viewer (Solo lectura en balances y transacciones)
        $viewerPermissions = [
            'MonthlyBalance:Read',
            'Transaction:Read'
        ];
        $viewer?->syncPermissions($viewerPermissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // NO eliminamos los roles porque ya existían antes de esta migración

        // Limpiamos los permisos creados siguiendo el patrón 'Modulo:Accion'
        DB::table(config('permission.table_names.permissions'))
            ->where('name', 'like', '%:%', 'and')
            ->delete();
    }
};