<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsList = config('permissions.permissions', []);
        $guardName = 'admin';
        $superAdminName = config('permissions.super_admin_role_name', Roles::SUPER_ADMIN->value);

        $this->command?->info('Starting Role & Permission synchronization (Seeder)...');

        // 1. Prepare Upsert Data
        $upsertData = [];
        $now = now();
        foreach ($permissionsList as $permission) {
            $upsertData[] = [
                'name' => $permission,
                'guard_name' => $guardName,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // 2. Perform Upsert (Insert or Ignore/Update)
        // using the Permission model's table name
        $tableName = config('permission.table_names.permissions');
        DB::table($tableName)->upsert(
            $upsertData,
            ['name', 'guard_name'], // Unique by name + guard
            ['updated_at']          // Just touch updated_at if exists
        );

        if ($this->command) {
            $this->command->info('Upserted '.count($upsertData).' permissions from config.');
        }

        // 3. Remove stale permissions (in DB but not in config)
        $deletedCount = DB::table($tableName)
            ->where('guard_name', $guardName)
            ->whereNotIn('name', $permissionsList)
            ->delete();

        if ($deletedCount > 0) {
            $this->command?->warn('Deleted '.$deletedCount.' stale permissions from the database.');
        }

        // 4. Ensure a Super Admin role exists and has all permissions
        $superAdmin = Role::firstOrCreate([
            'name' => $superAdminName,
            'guard_name' => $guardName,
        ]);

        $superAdmin->syncPermissions(Permission::where('guard_name', $guardName)->get());

        // Clear permission cache
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Synchronization complete.');
    }
}
