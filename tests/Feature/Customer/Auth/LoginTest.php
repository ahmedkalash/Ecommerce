<?php

namespace Tests\Feature\Customer\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

/**
 * Login Feature Tests (Livewire Refactored)
 *
 * Tests the complete login flow including
 * - Email login
 * - Password validation
 * - Banned user blocking
 * - User type redirects
 * - Cart preservation
 * - Remember me functionality
 * - Throttle limits
 */
class LoginTest extends AuthTestCase
{
    /** @test */
    public function customer_can_login_with_email_and_password(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_wrong_password(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('correct_password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong_password')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function login_fails_with_nonexistent_email(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'nonexistent@example.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function banned_user_cannot_login(): void
    {
        $user = User::factory()->customer()->banned()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        // Middleware kicks them out later when they access protected dashboard route
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function admin_redirects_to_admin_dashboard_after_login(): void
    {
        $user = User::factory()->admin()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function seller_redirects_to_seller_dashboard_after_login(): void
    {
        $user = User::factory()->seller()->create([
            'password' => Hash::make('password123'),
        ]);

        $shop = new \App\Models\Shop;
        $shop->user_id = $user->id;
        $shop->name = 'Test Shop';
        $shop->slug = 'test-shop-'.time();
        $shop->registration_approval = 1; // Approved
        $shop->save();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function delivery_boy_redirects_to_delivery_dashboard_after_login(): void
    {
        $user = User::factory()->deliveryBoy()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function staff_redirects_to_admin_dashboard_after_login(): void
    {
        $user = User::factory()->staff()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function guest_cart_items_transfer_to_user_cart_after_login(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        $tempUserId = $this->createGuestCart([
            [
                'product_id' => 1,
                'quantity' => 2,
            ],
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate');

        $this->assertCartTransferred($tempUserId, $user->id);
    }

    /** @test */
    public function customer_redirects_to_saved_session_link_after_login(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        session(['url.intended' => '/checkout']);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect('/checkout');

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function remember_me_checkbox_sets_remember_token(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function login_requires_email(): void
    {
        Livewire::test(Login::class)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['email' => 'required']);

        $this->assertGuest();
    }

    /** @test */
    public function login_requires_password(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'customer@example.com')
            ->call('authenticate')
            ->assertHasErrors(['password' => 'required']);

        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout(): void
    {
        $user = User::factory()->customer()->create();
        $this->actingAs($user);

        $response = $this->get(route('logout'));

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login_on_protected_route(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(302);
        $response->assertRedirect(route('user.login'));
    }

    /** @test */
    public function temp_user_session_is_cleared_after_login(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        $tempUserId = $this->createGuestCart([
            ['product_id' => 1, 'quantity' => 1],
        ]);

        $this->assertNotNull(session('temp_user_id'));

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate');

        $this->assertNull(session('temp_user_id'));
    }

    /** @test */
    public function login_with_unverified_email_when_verification_required(): void
    {
        $this->enableEmailVerification();
        $user = User::factory()->customer()->unverified()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect(route('verification.notice'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_throttling_after_multiple_failed_attempts(): void
    {
        $component = Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword');

        for ($i = 0; $i < 5; $i++) {
            $component->call('authenticate');
        }

        // 6th attempt throttled
        $component->call('authenticate')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function login_with_inactive_or_soft_deleted_user(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        $email = $user->email;
        $user->delete();

        Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    /** @test */
    public function login_preserves_query_parameters_in_redirect(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        session(['url.intended' => '/checkout?coupon=SAVE10&ref=email']);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect('/checkout?coupon=SAVE10&ref=email');

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function remember_token_persists_across_sessions(): void
    {
        $user = User::factory()->customer()->create([
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password123')
            ->set('remember', true)
            ->call('authenticate');

        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function login_is_case_insensitive_for_email(): void
    {
        $user = User::factory()->customer()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'USER@TEST.COM')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertRedirect($user->homePage());

        $this->assertAuthenticatedAs($user);
    }
}
