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
        $superAdminName = Roles::SUPER_ADMIN->value;

        if ($superAdminRole = Role::where('name', $superAdminName)->where('guard_name', 'admin')->first()) {
            $email = 'Ahmedkalash513@gmail.com';
            $this->createSuperAdmin($email, $email);

            if ($admin = Admin::find(1)) {
                $admin->assignRole($superAdminRole);

                $this->command?->info("Role '{$superAdminName}' assigned to Admin ID 1, Email: $email, Password: \"$email\".");
            }
        } else {
            $this->command?->warn("Super Admin role '{$superAdminName}' not found. Please run RoleAndPermissionSeeder first.");
        }
    }

    private function createSuperAdmin(string $email, string $password): void
    {
        Admin::upsert(
            [
                [
                    'id' => 1,
                    'name' => 'Ahmed Kalash',
                    'email' => $email,
                    'password' => Hash::make($password),
                    'user_type' => UserType::ADMIN->value,
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['id'],
            ['name', 'email', 'password', 'user_type', 'updated_at']
        );
    }
}
