<?php

namespace Database\Seeders;

use App\Enums\Roles;
use App\Enums\UserType;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AssignSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminName = config('permissions.super_admin_role_name', Roles::SUPER_ADMIN->value);
        $guardName = 'admin';

        $superAdminRole = Role::where('name', $superAdminName)->where('guard_name', $guardName)->first();

        if ($superAdminRole) {
            $email = 'Ahmedkalash513@gmail.com';
            Admin::upsert(
                [
                    [
                        'id' => 1,
                        'name' => 'Ahmed Kalash',
                        'email' => $email,
                        'password' => Hash::make($email),
                        'user_type' => UserType::ADMIN->value,
                        'email_verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ],
                ['id'],
                ['name', 'email', 'password', 'user_type', 'updated_at']
            );

            if ($admin = Admin::find(1)) {
                $admin->assignRole($superAdminRole);
                $this->command?->info("Role '{$superAdminName}' assigned to Admin ID 1, Email: $email, Password: \"$email\".");
            }
        } else {
            $this->command?->warn("Super Admin role '{$superAdminName}' not found. Please run RoleAndPermissionSeeder first.");
        }
    }
}
