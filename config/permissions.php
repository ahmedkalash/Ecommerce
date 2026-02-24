<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | Define all permissions the application needs here. They are synced to
    | the database by the RoleAndPermissionSeeder — no manual DB entry needed.
    |
    |--------------------------------------------------------------------------
    | NAMING CONVENTION (Filament + Global Gate)
    |--------------------------------------------------------------------------
    |
    | The AuthServiceProvider Global Gate translates standard Laravel ability
    | checks into Spatie permission strings using this pattern: {snake_ability}_{snake_model}
    |
    | For example, when Filament checks `viewAny` on a `Coupon` model, the
    | Global Gate looks for a permission named `view_any_coupon`.
    |
    | STANDARD TEMPLATE — replace {model} with the snake_case, singular
    | model name (e.g. coupon, product, category, order):
    |
    |   '{ResourcePlural}' => [
    |       'view_any_{model}',          // List / index page
    |       'view_{model}',              // Show / detail page
    |       'create_{model}',            // Create page
    |       'update_{model}',            // Edit page
    |       'delete_{model}',            // Single delete
    |       'delete_any_{model}',        // Bulk delete
    |       'restore_{model}',           // Restore soft-deleted
    |       'restore_any_{model}',       // Bulk restore
    |       'force_delete_{model}',      // Permanently delete
    |       'force_delete_any_{model}',  // Bulk permanently delete
    |   ],
    |
    | RULES:
    |  1. Model name is ALWAYS singular and snake_case (e.g., product_stock).
    |  2. Ability is snake_case of the Laravel policy method (viewAny → view_any).
    |  3. You may omit permissions your resource doesn't need (e.g., skip restore/force_delete if the model doesn't use SoftDeletes).
    |  4. Custom / non-CRUD permissions are allowed (see 'Settings', 'Dashboard').
    |     They must still exist here, so the Global Gate doesn't throw PermissionDoesNotExist.
    |
    */

    'permissions' => [
        'Roles' => [
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
        ],
        'Coupons' => [
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
        ],
        'Admins' => [
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
        ],
        'Settings' => [
            'view_any_setting',
            'view_setting',
            'update_setting',
            'smtp_settings',
        ],
        'Dashboard' => [
            'admin_dashboard',
        ],
        // Add additional models and module prefixes here...
    ],
];
