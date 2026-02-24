<?php

use App\Enums\Roles;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | Define all the permissions the application needs here.
    | They will be synced to the database using the
    | RoleAndPermissionSeeder to avoid manual entry.
    |
    */

    'permissions' => [

        // Roles
        'view_any_role',
        'view_role',
        'create_role',
        'update_role',
        'delete_role',
        'delete_any_role',

        // Coupons
        'view_any_coupon',
        'view_coupon',
        'create_coupon',
        'update_coupon',
        'delete_coupon',
        'delete_any_coupon',
        'restore_coupon',
        'restore_any_coupon',
        'force_delete_coupon',
        'force_delete_any_coupon',

        // Admins
        'view_any_admin',
        'view_admin',
        'create_admin',
        'update_admin',
        'delete_admin',
        'delete_any_admin',
        'restore_admin',
        'restore_any_admin',
        'force_delete_admin',
        'force_delete_any_admin',

        // Settings (e.g. system configurations, not specific to a resource)
        'view_any_setting',
        'view_setting',
        'update_setting',

        // Add additional models and module prefixes here...
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin Configuration
    |--------------------------------------------------------------------------
    |
    | The name of the super administrator role.
    | This role bypasses standard permission checks.
    |
    */
    'super_admin_role_name' => Roles::SUPER_ADMIN->value,
];
