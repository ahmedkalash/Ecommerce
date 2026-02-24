<?php

namespace Tests\Feature\Filament;

use App\Models\Admin;
use App\Models\User;
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

    public function test_super_admin_bypasses_all_gate_checks()
    {
        $user = User::factory()->create([
            'user_type' => 'admin',
        ]);
        $admin = Admin::find($user->id);

        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);
        $admin->assignRole($role);

        $permission = Permission::firstOrCreate(['name' => 'view_any_coupon', 'guard_name' => 'admin']);
        // Notice we do NOT give this permission to the super_admin explicitly.

        $this->assertTrue($admin->can('viewAny', \App\Models\Coupon::class));
    }

    public function test_dynamic_gate_throws_exception_on_unregistered_permission()
    {
        $user = User::factory()->create(['user_type' => 'staff']);
        $staff = Admin::find($user->id);

        $this->expectException(\Spatie\Permission\Exceptions\PermissionDoesNotExist::class);
        $this->expectExceptionMessage("Neither custom permission 'view_any_user' nor standard permission 'viewAny' exists.");

        // This should throw because neither 'view_any_user' nor 'viewAny' exist in the database
        $staff->can('viewAny', User::class);
    }

    public function test_fallback_ability_name_is_checked_if_primary_model_mapping_fails()
    {
        $user = User::factory()->create(['user_type' => 'staff']);
        $staff = Admin::find($user->id);

        // We register the exact ability name as a fallback instead of model mapping
        $permission = Permission::firstOrCreate(['name' => 'custom_random_ability', 'guard_name' => 'admin']);
        $staff->givePermissionTo($permission);

        // Even though it maps 'custom_random_ability' -> 'custom_random_ability_',
        // it falls back to 'custom_random_ability' and finds it.
        $this->assertTrue($staff->can('custom_random_ability'));

        // Conversely, if the user does NOT have the fallback ability, it returns false
        $user2 = User::factory()->create(['user_type' => 'staff']);
        $otherStaff = Admin::find($user2->id);
        $this->assertFalse($otherStaff->can('custom_random_ability'));
    }

    public function test_access_admin_panel_gate_allows_valid_users()
    {
        $admin = User::factory()->create(['user_type' => 'admin', 'banned' => 0]);
        $staff = User::factory()->create(['user_type' => 'staff', 'banned' => 0]);
        $customer = User::factory()->create(['user_type' => 'customer', 'banned' => 0]);
        $bannedStaff = User::factory()->create(['user_type' => 'staff', 'banned' => 1]);

        $this->assertTrue(\Illuminate\Support\Facades\Gate::forUser($admin)->allows('access-admin-panel'));
        $this->assertTrue(\Illuminate\Support\Facades\Gate::forUser($staff)->allows('access-admin-panel'));

        $this->assertFalse(\Illuminate\Support\Facades\Gate::forUser($customer)->allows('access-admin-panel'));
        $this->assertFalse(\Illuminate\Support\Facades\Gate::forUser($bannedStaff)->allows('access-admin-panel'));
    }
}
