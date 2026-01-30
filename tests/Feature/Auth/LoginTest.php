<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Login Feature Tests
 *
 * Tests the complete login flow including:
 * - Email/phone login
 * - Password validation
 * - Banned user blocking
 * - User type redirects
 * - Cart preservation
 * - Remember me functionality
 */
class LoginTest extends AuthTestCase
{
    /**
     * Test: Customer can login with email and password
     *
     * @test
     */
    public function customer_can_login_with_email_and_password(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Customer can login with phone and password
     *
     * @test
     *
     * @preserveGlobalState disabled
     */
    public function customer_can_login_with_phone_and_password(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'phone' => '+11234567890',
            'email' => null, // Phone-only account
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'phone' => '+11234567890',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Login fails with wrong password
     *
     * @test
     */
    public function login_fails_with_wrong_password(): void
    {
        // Arrange
        User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'wrong_password',
        ]);

        // Assert
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test: Login fails with non-existent email
     *
     * @test
     */
    public function login_fails_with_nonexistent_email(): void
    {
        // Act
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test: Banned user cannot login
     *
     * @test
     */
    public function banned_user_cannot_login(): void
    {
        // Arrange
        $user = User::factory()->customer()->banned()->create([
            'email' => 'banned@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'banned@example.com',
            'password' => 'password123',
        ]);

        // Assert
        // The middleware should block banned users
        $this->assertGuest();
    }

    /**
     * Test: Admin redirects to admin dashboard after login
     *
     * @test
     */
    public function admin_redirects_to_admin_dashboard_after_login(): void
    {
        // Arrange
        $user = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Seller redirects to seller dashboard after login
     *
     * @test
     */
    public function seller_redirects_to_seller_dashboard_after_login(): void
    {
        // Arrange
        $user = User::factory()->seller()->create([
            'email' => 'seller@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'seller@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/seller/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Delivery boy redirects to delivery dashboard after login
     *
     * @test
     */
    public function delivery_boy_redirects_to_delivery_dashboard_after_login(): void
    {
        // Arrange
        $user = User::factory()->deliveryBoy()->create([
            'email' => 'delivery@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'delivery@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/delivery-boys/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Staff redirects to admin dashboard after login
     *
     * @test
     */
    public function staff_redirects_to_admin_dashboard_after_login(): void
    {
        // Arrange
        $user = User::factory()->staff()->create([
            'email' => 'staff@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'staff@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Guest cart items transfer to user cart after login
     *
     * @test
     */
    public function guest_cart_items_transfer_to_user_cart_after_login(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create guest cart
        $tempUserId = $this->createGuestCart([
            [
                'product_id' => 1,
                'quantity' => 2,
            ],
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $this->assertCartTransferred($tempUserId, $user->id);
    }

    /**
     * Test: Customer redirects to saved session link after login
     *
     * @test
     */
    public function customer_redirects_to_saved_session_link_after_login(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
        ]);

        session(['link' => '/checkout']);

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/checkout');
    }

    /**
     * Test: Remember me checkbox sets remember token
     *
     * @test
     */
    public function remember_me_checkbox_sets_remember_token(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
            'remember' => 'on',
        ]);

        // Assert
        $this->assertAuthenticatedAs($user);

        // Check if remember token is set (indicates remember me worked)
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /**
     * Test: Login requires email or phone
     *
     * @test
     */
    public function login_requires_email_or_phone(): void
    {
        // Act
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test: Login requires password
     *
     * @test
     */
    public function login_requires_password(): void
    {
        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
        ]);

        // Assert
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
     * Test: User can logout
     *
     * @test
     */
    public function user_can_logout(): void
    {
        // Arrange
        $user = User::factory()->customer()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get('/logout');

        // Assert
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /**
     * Test: Unauthenticated user redirected to login
     *
     * @test
     */
    public function unauthenticated_user_redirected_to_login_on_protected_route(): void
    {
        // Act
        $response = $this->get('/dashboard');

        // Assert
        $response->assertRedirect('/login');
    }

    /**
     * Test: Temp user session is cleared after login
     *
     * @test
     */
    public function temp_user_session_is_cleared_after_login(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
        ]);

        $tempUserId = $this->createGuestCart([
            ['product_id' => 1, 'quantity' => 1],
        ]);

        $this->assertNotNull(session('temp_user_id'));

        // Act
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $this->assertNull(session('temp_user_id'));
    }
}
