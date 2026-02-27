<?php

namespace Tests\Unit;

use App\DTOs\StaffDTO;
use App\Models\Admin;
use App\Services\StaffService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected StaffService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StaffService::class);
    }

    public function test_it_can_store_staff_without_roles()
    {
        $dto = StaffDTO::fromArray([
            'name' => 'John Doe',
            'email' => 'john@admin.com',
            'phone' => '1234567890',
            'password' => 'password123',
            'user_type' => 'staff',
        ]);

        $admin = $this->service->store($dto);

        $this->assertInstanceOf(Admin::class, $admin);
        $this->assertEquals('John Doe', $admin->name);
        $this->assertEquals('john@admin.com', $admin->email);
        $this->assertEquals('staff', $admin->user_type);
        $this->assertTrue(Hash::check('password123', $admin->password));
        $this->assertCount(0, $admin->roles);
    }

    public function test_it_can_store_staff_with_roles()
    {
        $role1 = Role::create(['name' => 'Catalog Manager', 'guard_name' => 'admin']);
        $role2 = Role::create(['name' => 'Editor', 'guard_name' => 'admin']);

        $dto = StaffDTO::fromArray([
            'name' => 'Jane Staff',
            'email' => 'jane@admin.com',
            'phone' => '1234567890',
            'password' => 'password',
            'user_type' => 'admin',
            'roles' => [$role1->id, $role2->id],
        ]);

        $admin = $this->service->store($dto);

        $this->assertTrue($admin->hasRole('Catalog Manager'));
        $this->assertTrue($admin->hasRole('Editor'));
        $this->assertCount(2, $admin->roles);
    }

    public function test_it_can_update_staff()
    {
        $admin = Admin::create([
            'name' => 'Old Name',
            'email' => 'old@admin.com',
            'password' => Hash::make('password'),
            'user_type' => 'staff',
        ]);

        $dto = StaffDTO::fromArray([
            'name' => 'New Name',
            'email' => 'new@admin.com',
            'phone' => '0987654321',
            'user_type' => 'admin',
        ]);

        $updated = $this->service->update($dto, $admin);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals('new@admin.com', $updated->email);
        $this->assertEquals('admin', $updated->user_type);
    }
}
