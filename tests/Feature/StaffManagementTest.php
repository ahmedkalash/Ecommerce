<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffManagementTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected Admin $admin;

    protected Role $staffRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a super admin to run tests
        $this->admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin_'.uniqid().'@example.com',
            'user_type' => UserType::ADMIN->value,
            'password' => bcrypt('password'),
        ]);

        // Ensure Super Admin role exists and has permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $permissions = [
            'view_all_staffs', 'add_staff', 'edit_staff', 'delete_staff',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin', 'group' => 'staff']);
        }

        $superAdminRole->syncPermissions($permissions);
        $this->admin->assignRole($superAdminRole);

        // Create a role for the staff we will create
        $this->staffRole = Role::firstOrCreate(['name' => 'Store Manager', 'guard_name' => 'admin']);
    }

    protected function tearDown(): void
    {
        // Cleanup created users
        User::where('email', 'like', 'test_%')->delete();
        $this->admin->delete();
        parent::tearDown();
    }

    /** @test */
    public function index_page_is_accessible()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('staffs.index'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staffs.index');
    }

    /** @test */
    public function create_page_is_accessible()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('staffs.create'));
        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staffs.create');
    }

    /** @test */
    public function a_new_staff_can_be_stored()
    {
        $email = 'test_staff_'.uniqid().'@example.com';
        $data = [
            'name' => 'Test Staff',
            'email' => $email,
            'mobile' => '123456789',
            'password' => 'password123',
            'role_id' => $this->staffRole->id,
        ];

        $response = $this->actingAs($this->admin, 'admin')->post(route('staffs.store'), $data);

        $response->assertRedirect(route('staffs.index'));
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'user_type' => UserType::STAFF->value,
        ]);

        $user = Admin::where('email', $email)->first();
        $this->assertTrue($user->hasRole($this->staffRole->name, 'admin'));
    }

    /** @test */
    public function edit_page_is_accessible()
    {
        $staff = User::factory()->create(['user_type' => UserType::STAFF->value]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('staffs.edit', $staff->id));

        $response->assertStatus(200);
        $response->assertViewIs('backend.staff.staffs.edit');
        $response->assertViewHas('staff');
    }

    /** @test */
    public function staff_can_be_updated()
    {
        $staff = User::factory()->create(['user_type' => UserType::STAFF->value]);
        $newEmail = 'updated_test_'.uniqid().'@example.com';

        $data = [
            'name' => 'Updated Name',
            'email' => $newEmail,
            'mobile' => '987654321',
            'role_id' => $this->staffRole->id,
        ];

        $response = $this->actingAs($this->admin, 'admin')->put(route('staffs.update', $staff->id), $data);

        $response->assertRedirect(route('staffs.index'));
        $this->assertDatabaseHas('users', [
            'id' => $staff->id,
            'email' => $newEmail,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function staff_can_be_deleted()
    {
        $staff = User::factory()->create(['user_type' => UserType::STAFF->value]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('staffs.destroy', $staff->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    /** @test */
    public function deletion_fails_gracefully_for_non_existent_staff()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('staffs.destroy', 999999));

        $response->assertRedirect();
    }
}
