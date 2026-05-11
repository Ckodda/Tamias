<?php

use Spatie\Permission\DefaultTeamResolver;

return [

    'models' => [
        'permission' => App\Models\Permission::class,
        'role' => App\Models\Role::class,
        'team' => null,
        'default_model' => null,
    ],

    'table_names' => [
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'model_has_permissions' => 'ModelHasPermissions',
        'model_has_roles' => 'ModelHasRoles',
        'role_has_permissions' => 'RoleHasPermissions',
        'users' => 'Users', // Added this line
    ],

    'column_names' => [
        'role_pivot_key' => 'RoleId',
        'permission_pivot_key' => 'PermissionId',
        'model_morph_key' => 'ModelId',
        'team_foreign_key' => 'TeamId',
    ],

    'register_permission_check_method' => true,
    'register_octane_reset_listener' => false,
    'events_enabled' => false,
    'teams' => false,
    'team_resolver' => DefaultTeamResolver::class,
    'use_passport_client_credentials' => false,
    'display_permission_in_exception' => false,
    'display_role_in_exception' => false,
    'enable_wildcard_permission' => false,

    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];
