<?php

namespace Tests\Feature;

use App\Models\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a super admin to run tests
        $user = \App\Models\User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin_'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->admin = Admin::find($user->id);

        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $permissions = [
            'view_staff_roles', 'add_staff_role', 'edit_staff_role', 'delete_staff_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin', 'group' => 'staff']);
        }

        $superAdminRole->syncPermissions($permissions);
        $this->admin->assignRole($superAdminRole);

        // Crucial: Clear permission cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function tearDown(): void
    {
        $this->admin->delete();
        parent::tearDown();
    }

    /** @test */
    public function role_index_page_is_accessible()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('roles.index'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staff_roles.index');
    }

    /** @test */
    public function role_create_page_is_accessible()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('roles.create'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staff_roles.create');
    }

    /** @test */
    public function a_new_role_can_be_stored()
    {
        // Create some permissions to assign
        $perm1 = Permission::firstOrCreate(['name' => 'perm_1', 'guard_name' => 'admin', 'group' => 'test']);
        $perm2 = Permission::firstOrCreate(['name' => 'perm_2', 'guard_name' => 'admin', 'group' => 'test']);

        $roleName = 'Test Role '.uniqid();
        $data = [
            'name' => $roleName,
            'permissions' => ['perm_1', 'perm_2'],
        ];

        $response = $this->actingAs($this->admin, 'admin')->post(route('roles.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => $roleName]);

        $role = Role::findByName($roleName, 'admin');
        $this->assertTrue($role->hasPermissionTo('perm_1'));
        $this->assertTrue($role->hasPermissionTo('perm_2'));
    }

    /** @test */
    public function role_edit_page_is_accessible()
    {
        $role = Role::create(['name' => 'Editable Role '.uniqid(), 'guard_name' => 'admin']);

        $response = $this->actingAs($this->admin, 'admin')->get(route('roles.edit', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staff_roles.edit');
        $response->assertViewHas('role');
    }

    /** @test */
    public function role_can_be_updated()
    {
        $role = Role::create(['name' => 'Old Role Name '.uniqid(), 'guard_name' => 'admin']);
        $newRoleName = 'New Role Name '.uniqid();

        $perm = Permission::firstOrCreate(['name' => 'perm_3', 'guard_name' => 'admin', 'group' => 'test']);

        $data = [
            'name' => $newRoleName,
            'permissions' => ['perm_3'],
        ];

        $response = $this->actingAs($this->admin, 'admin')->put(route('roles.update', $role->id), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => $newRoleName,
        ]);

        $updatedRole = Role::findById($role->id, 'admin');
        $this->assertTrue($updatedRole->hasPermissionTo('perm_3'));
    }

    /** @test */
    public function role_can_be_deleted()
    {
        $role = Role::create(['name' => 'Deletable Role '.uniqid(), 'guard_name' => 'admin']);

        $response = $this->actingAs($this->admin, 'admin')->get(route('roles.destroy', $role->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /** @test */
    public function a_new_permission_can_be_added()
    {
        $permName = 'new_test_perm_'.uniqid();
        $groupName = 'test_group';

        $data = [
            'name' => $permName,
            'parent' => $groupName, // View uses 'parent' but request maps it to 'group' in validation if updated?
            // Wait, the AddPermissionRequest used 'group' but the view uses 'parent'.
        ];

        // I need to check the add_permission method in RoleController and AddPermissionRequest again.
        // Controller: 'group' => $request->group
        // Request: 'group' => ['required', ...]
        // So the data should have 'group' key.

        $data = [
            'name' => $permName,
            'group' => $groupName,
        ];

        $response = $this->actingAs($this->admin, 'admin')->post(route('roles.permission'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', [
            'name' => $permName,
            'group' => $groupName,
        ]);
    }
}
