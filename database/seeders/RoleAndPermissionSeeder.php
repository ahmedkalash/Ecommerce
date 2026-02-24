<?php

namespace Database\Seeders;

use App\Enums\Roles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
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
        $permissionsList = Arr::flatten(config('permissions.permissions', []));
        $guardName = 'admin';
        $superAdminName = Roles::SUPER_ADMIN->value;

        $this->command?->info('Starting Role & Permission synchronization (Seeder)...');

        // 1. Prepare Upsert Data
        $upsertData = $this->prepareUpsertData($permissionsList, $guardName);

        // 2. Perform Upsert using the Permission model's table name
        // and remove stale permissions (in DB but not in config)
        $this->performUpsert($upsertData, $guardName, $permissionsList);

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

    private function prepareUpsertData(array $permissionsList, string $guardName): array
    {

        $now = now();
        $upsertData = [];
        foreach ($permissionsList as $permission) {
            $upsertData[] = [
                'name' => $permission,
                'guard_name' => $guardName,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $upsertData;
    }

    private function performUpsert(array $upsertData, string $guardName, array $permissionsList): void
    {
        $tableName = config('permission.table_names.permissions');
        DB::table($tableName)->upsert(
            $upsertData,
            ['name', 'guard_name'], // Unique by name + guard
            ['updated_at']          // Just touch updated_at if exists
        );

        if ($this->command) {
            $this->command->info('Upserted '.count($upsertData).' permissions from config.');
        }

        $deletedCount = DB::table($tableName)
            ->where('guard_name', $guardName)
            ->whereNotIn('name', $permissionsList)
            ->delete();

        if ($deletedCount > 0) {
            $this->command?->warn('Deleted '.$deletedCount.' stale permissions from the database.');
        }
    }
}
