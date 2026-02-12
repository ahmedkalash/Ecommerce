<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role as SpatieRole;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure the role exists for tests that might need it contextually
        SpatieRole::firstOrCreate(['name' => 'Test Manager', 'guard_name' => 'admin']);
    }

    /** @test */
    public function admin_guard_is_configured_correctly()
    {
        $config = config('auth.guards.admin');
        $this->assertEquals('session', $config['driver']);
        $this->assertEquals('admins', $config['provider']);

        // Verify the provider uses Admin model
        $providerConfig = config('auth.providers.admins');
        $this->assertEquals(Admin::class, $providerConfig['model']);
    }

    /** @test */
    public function admin_model_only_retrieves_admin_and_staff_users()
    {
        // Create test users with factory-generated unique emails
        $adminUser = User::factory()->admin()->create();
        $staffUser = User::factory()->staff()->create();
        $customerUser = User::factory()->customer()->create();

        // Admin model should only retrieve admin and staff
        $adminResults = Admin::whereIn('id', [$adminUser->id, $staffUser->id, $customerUser->id])->get();

        $this->assertCount(2, $adminResults);
        $this->assertTrue($adminResults->contains('id', $adminUser->id));
        $this->assertTrue($adminResults->contains('id', $staffUser->id));
        $this->assertTrue($adminResults->contains('id', $staffUser->id));
        $this->assertFalse($adminResults->contains('id', $customerUser->id));
    }

    /** @test */
    public function middleware_blocks_non_admin_users()
    {
        $user = User::factory()->customer()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin');

        $response->assertStatus(403);
    }

    /** @test */
    public function middleware_allows_admin_users()
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin');

        $response->assertStatus(200);
    }

    /** @test */
    public function staff_can_access_admin_panel()
    {
        $user = User::factory()->staff()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin');

        $response->assertStatus(200);
    }

    /** @test */
    public function banned_staff_cannot_access_admin_panel()
    {
        $user = User::factory()->staff()->banned()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin');

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHas('flash_notification');
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }
}
