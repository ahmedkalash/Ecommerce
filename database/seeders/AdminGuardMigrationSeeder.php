<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AdminGuardMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder migrates the permissions and roles infrastructure
     * from the implicit 'web' guard to the explicit 'admin' guard
     * for all staff and admin users.
     *
     * It ensures:
     * 1. All permissions exist for 'admin' guard
     * 2. All roles exist for 'admin' guard
     * 3. Admin roles have the correct permissions
     * 4. Admin/Staff users are assigned roles using the 'App\Models\Admin' model type
     */
    public function run()
    {
        // Clear cache first
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('Starting Admin Guard Migration...');

        // 1. Replicate Permissions to 'admin' guard
        $webPermissions = Permission::where('guard_name', 'web')->get();
        $this->command->info("Found {$webPermissions->count()} web permissions. replicating to admin guard...");

        foreach ($webPermissions as $perm) {
            Permission::firstOrCreate([
                'name' => $perm->name,
                'guard_name' => 'admin'
            ]);
        }

        // 2. Replicate Roles to 'admin' guard and sync permissions
        $webRoles = Role::where('guard_name', 'web')->get();
        $this->command->info("Found {$webRoles->count()} web roles. Replicating to admin guard...");

        foreach ($webRoles as $webRole) {
            $adminRole = Role::firstOrCreate([
                'name' => $webRole->name,
                'guard_name' => 'admin'
            ]);

            // Sync permissions
            $webRolePermissions = $webRole->permissions->pluck('name');
            $adminRole->syncPermissions($webRolePermissions);
        }

        // 3. Migrate Users (Admins & Staff)
        // We need to update the model_has_roles table.
        // The Admin model uses 'App\Models\Admin' as its polymorphic type (by default).
        // Previously, roles were assigned to 'App\Models\User' (because we used User model or overridden Admin model).
        // We need to move these assignments.

        $adminUsers = User::whereIn('user_type', ['admin', 'staff'])->get();
        $this->command->info("Processing {$adminUsers->count()} admin/staff users...");

        foreach ($adminUsers as $user) {
            // Find existing roles on 'web' guard for this user being treated as 'App\Models\User'
            // OR 'App\Models\Admin' if any exist.

            // We'll perform a direct DB query to see what they have assigned as 'App\Models\User'
            $existingAssignments = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', 'App\Models\User')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', 'roles.guard_name')
                ->get();

            foreach ($existingAssignments as $assignment) {
                // If they have a role on web guard, assign the equivalent role on admin guard
                // using the 'App\Models\Admin' model type.

                // We use the Admin model to attach the role, which automatically handles
                // the guard (via config/auth.php -> defaults -> admin -> provider -> Admin model)
                // AND the morph class (App\Models\Admin).

                // However, we can't easily instantiate Admin objects here and use assignRole
                // reliably without possibly tripping up on the current state.
                // It's safer to insert directly into DB to guarantee exact state.

                $adminRole = Role::where('name', $assignment->name)
                    ->where('guard_name', 'admin')
                    ->first();

                if ($adminRole) {
                    // Check if already assigned
                    $exists = DB::table('model_has_roles')
                        ->where('role_id', $adminRole->id)
                        ->where('model_type', 'App\Models\Admin')
                        ->where('model_id', $user->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $adminRole->id,
                            'model_type' => 'App\Models\Admin',
                            'model_id' => $user->id
                        ]);
                        $this->command->info("Assigned '{$adminRole->name}' (admin) to User ID {$user->id} as App\Models\Admin");
                    }
                }
            }
        }

        // Cleanup: Ideally we might remove the old 'App\Models\User' assignments for these users on the 'web' guard
        // if they should NEVER log in as 'web'. But keeping them doesn't hurt and allows fallback if needed.
        // For strictness, we could remove them, but safety first.

        $this->command->info('Admin Guard Migration Completed Successfully.');

        // Clear cache again
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
