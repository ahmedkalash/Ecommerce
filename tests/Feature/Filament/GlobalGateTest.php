<?php

namespace Tests\Feature\Filament;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GlobalGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_intercepts_permission_checks_properly()
    {
        $staff = Admin::create([
            'name' => 'Gate Tester',
            'email' => 'gate@admin.com',
            'password' => bcrypt('password'),
            'user_type' => 'staff',
        ]);

        $role = Role::create(['name' => 'Limited', 'guard_name' => 'admin']);
        $permission = Permission::firstOrCreate(['name' => 'view_any_coupon', 'guard_name' => 'admin']);

        $role->givePermissionTo($permission);

        // Before assigning role
        $this->assertFalse($staff->can('viewAny', \App\Models\Coupon::class));

        // After assigning role
        $staff->assignRole($role);
        $this->assertTrue($staff->fresh()->can('viewAny', \App\Models\Coupon::class));
    }

    public function test_filament_honors_global_gate_for_pages()
    {
        $staff = Admin::create([
            'name' => 'Gate Tester 2',
            'email' => 'gate2@admin.com',
            'password' => bcrypt('password'),
            'user_type' => 'staff',
        ]);

        $this->actingAs($staff, 'admin');

        // We must create the permission in the DB so AuthServiceProvider doesn't throw a PermissionDoesNotExist exception under the new strict rules
        $permissions = [
            'view_any_coupon',
            'view_coupon',
            'create_coupon',
            'update_coupon',
            'delete_coupon',
            'delete_any_coupon',
            'force_delete_coupon',
            'force_delete_any_coupon',
            'restore_coupon',
            'restore_any_coupon',
            'reorder_coupon',
            'reorder',
            'page_ListCoupons',
            'view_any_category',
            'view_any_product',
            'view_any_role',
            'view_any_admin',
            'page_Dashboard',
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'admin']);
        }

        // Without assigning the permission to the user, it should be Forbidden (403)
        $this->get(\App\Filament\Resources\CouponResource::getUrl('index'))
            ->assertForbidden();

        // Give permission
        $role = Role::create(['name' => 'Viewer', 'guard_name' => 'admin']);

        // Ensure the User has ALL the permissions we created, particularly page_ListCoupons which Filament needs to render the index page
        $allPermissions = Permission::whereIn('name', $permissions)->get();
        $role->givePermissionTo($allPermissions);

        $staff->assignRole($role);

        // With permission, HTTP request should render the page successfully (200)
        $this->withoutExceptionHandling();
        try {
            $this->get(\App\Filament\Resources\CouponResource::getUrl('index'))
                ->assertSuccessful();
        } catch (\Exception $e) {
            dump($e->getMessage());
            throw $e;
        }
    }
}
