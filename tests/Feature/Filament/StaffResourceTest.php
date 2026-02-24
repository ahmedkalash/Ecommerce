<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\StaffResource\Pages\CreateStaff;
use App\Filament\Resources\StaffResource\Pages\EditStaff;
use App\Filament\Resources\StaffResource\Pages\ListStaff;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Since Filament requires authentication
        $this->actingAs(User::factory()->create(['user_type' => 'admin']), 'admin');
    }

    public function test_can_render_staff_list()
    {
        Livewire::test(ListStaff::class)
            ->assertSuccessful();
    }

    public function test_can_create_staff()
    {
        $role = Role::create(['name' => 'Support', 'guard_name' => 'admin']);

        Livewire::test(CreateStaff::class)
            ->fillForm([
                'name' => 'New Guy',
                'email' => 'newguy@admin.com',
                'phone' => '1112223333',
                'password' => 'password123',
                'user_type' => 'staff',
                'roles' => [$role->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'newguy@admin.com',
            'user_type' => 'staff',
            'name' => 'New Guy',
        ]);

        $admin = Admin::where('email', 'newguy@admin.com')->first();
        $this->assertTrue(Hash::check('password123', $admin->password));
        $this->assertTrue($admin->hasRole('Support'));
    }

    public function test_can_update_staff()
    {
        $staff = Admin::create([
            'name' => 'Old Dave',
            'email' => 'dave@admin.com',
            'password' => Hash::make('password123'),
            'user_type' => 'staff',
        ]);

        $role = Role::create(['name' => 'Manager', 'guard_name' => 'admin']);
        $staff->assignRole($role);

        Livewire::test(EditStaff::class, [
            'record' => $staff->id,
        ])
            ->assertFormSet([
                'name' => 'Old Dave',
                'email' => 'dave@admin.com',
                'user_type' => 'staff',
                'roles' => [$role->id],
            ])
            ->fillForm([
                'name' => 'New Dave',
                'user_type' => 'admin',
                'roles' => [], // remove role
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $staff->refresh();
        $this->assertEquals('New Dave', $staff->name);
        $this->assertEquals('admin', $staff->user_type);
        $this->assertFalse($staff->hasRole('Manager'));
    }

    public function test_validates_unique_email()
    {
        Admin::create([
            'name' => 'Existing Guy',
            'email' => 'existing@admin.com',
            'password' => Hash::make('password123'),
            'user_type' => 'staff',
        ]);

        Livewire::test(CreateStaff::class)
            ->fillForm([
                'name' => 'New Guy',
                'email' => 'existing@admin.com', // Duplicate
                'phone' => '1112223333',
                'password' => 'password123',
                'user_type' => 'staff',
            ])
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique']);
    }
}
