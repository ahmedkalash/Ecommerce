<?php

namespace Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /** @test */
    public function admin_login_page_is_accessible(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertViewIs('backend.admin_login');
    }

    /** @test */
    public function admin_can_login_with_valid_credentials(): void
    {
        $password = 'secret-password-123';
        $user = User::factory()->admin()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect($user->homePage());
        $this->assertTrue(auth('admin')->check());
        $this->assertEquals($user->id, auth('admin')->id());
    }

    /** @test */
    public function admin_login_rejects_invalid_credentials(): void
    {
        $user = User::factory()->admin()->create([
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    /** @test */
    public function customer_cannot_login_via_admin_login(): void
    {
        $password = 'customer-password';
        $customer = User::factory()->customer()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->from(route('admin.login'))->post(route('admin.login'), [
            'email' => $customer->email,
            'password' => $password,
        ]);

        // The Admin provider only returns admin/staff users,
        // so a customer's credentials should fail authentication.
        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    /** @test */
    public function admin_can_logout(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user, 'admin')
            ->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    /** @test */
    public function admin_redirects_to_intended_url_after_login(): void
    {
        $password = 'secret-password-123';
        $user = User::factory()->admin()->create([
            'password' => bcrypt($password),
        ]);

        session(['url.intended' => '/admin/intended-url']);

        $response = $this->post(route('admin.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/admin/intended-url');
        $this->assertTrue(auth('admin')->check());
        $this->assertEquals($user->id, auth('admin')->id());
    }

    /** @test */
    public function already_authenticated_admin_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user, 'admin')->get(route('admin.login'));

        $response->assertRedirect($user->homePage());
    }
}
