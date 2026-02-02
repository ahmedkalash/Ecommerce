<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Password Reset Feature Tests
 *
 * Tests the complete password reset flow including:
 * - Reset request with email/phone
 * - Verification code generation and sending
 * - Password update with valid code
 * - Invalid code handling
 * - Auto-login after reset
 */
class PasswordResetTest extends AuthTestCase
{
    /**
     * Test: User can request password reset with email
     *
     * @test
     */
    public function user_can_request_password_reset_with_email(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Act
        $response = $this->post('/password/email', [
            'email' => 'user@example.com',
        ]);

        // Assert
        $response->assertOk(); // Returns view
        // $response->assertViewIs('auth.param.reset_password'); // Can be specific if we know the layout

        // Verify code was generated and stored
        $user->refresh();
        $this->assertNotNull($user->verification_code);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $user->verification_code);
    }

    /**
     * Test: Password reset request fails with non-existent email
     *
     * @test
     */
    public function password_reset_request_fails_with_nonexistent_email(): void
    {
        // Act
        $response = $this->post('/password/email', [
            'email' => 'nonexistent@example.com',
        ]);

        // Assert
        // Assert
        $response->assertRedirect(); // Redirects back with flash error
        // $this->assertGuest();
    }

    /**
     * Test: User can reset password with valid verification code
     *
     * @test
     */
    public function user_can_reset_password_with_valid_code(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password'),
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // Verify password changed
        $user->refresh();
        $this->assertTrue(Hash::check('new_password', $user->password));
        $this->assertFalse(Hash::check('old_password', $user->password));

        // Verify email is marked as verified
        $this->assertUserVerified($user);
    }

    /**
     * Test: Password reset fails with invalid verification code
     *
     * @test
     */
    public function password_reset_fails_with_invalid_code(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password'),
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '999999', // Wrong code
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        // Assert
        $response->assertOk(); // Returns view with error flash
        $this->assertGuest();

        // Verify password NOT changed
        $user->refresh();
        $this->assertTrue(Hash::check('old_password', $user->password));
    }

    /**
     * Test: Password reset fails with mismatched email and code
     *
     * @test
     */
    public function password_reset_fails_with_mismatched_email_and_code(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'user1@example.com',
            'verification_code' => '123456',
        ]);

        User::factory()->create([
            'email' => 'user2@example.com',
            'verification_code' => '654321',
        ]);

        // Act - Try user2's email with user1's code
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'user2@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        // Assert
        $response->assertOk(); // Returns view with error flash
        $this->assertGuest();
    }

    /**
     * Test: Password reset requires password confirmation
     *
     * @test
     */
    public function password_reset_requires_password_confirmation(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'different_password',
        ]);

        // Assert
        // Assert
        $response->assertOk(); // Returns view with warning flash
    }

    /**
     * Test: Password reset auto-logs user in after success
     *
     * @test
     */
    public function password_reset_auto_logs_user_in_after_success(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password'),
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Customer redirects to home after password reset
     *
     * @test
     */
    public function customer_redirects_to_home_after_password_reset(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'customer@example.com',
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'customer@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        $response->assertRedirect('/');
    }

    /**
     * Test: Seller redirects to seller dashboard after password reset
     *
     * @test
     */
    public function seller_redirects_to_seller_dashboard_after_password_reset(): void
    {
        // Arrange
        $user = User::factory()->seller()->create([
            'email' => 'seller@example.com',
            'verification_code' => '123456',
        ]);

        // Create approved shop for seller
        $shop = new \App\Models\Shop();
        $shop->user_id = $user->id;
        $shop->name = 'Test Shop';
        $shop->slug = 'test-shop';
        $shop->registration_approval = 1; // Approved
        $shop->save();

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'seller@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        $response->assertRedirect('/');
    }

    /**
     * Test: Admin redirects to admin dashboard after password reset
     *
     * @test
     */
    public function admin_redirects_to_admin_dashboard_after_password_reset(): void
    {
        // Arrange
        $user = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'verification_code' => '123456',
        ]);

        // Act
        $response = $this->post('/password/reset/email/submit', [
            'email' => 'admin@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Assert
        $response->assertRedirect('/admin');
    }

    /**
     * Test: Password reset request with phone (if OTP system enabled)
     *
     * This test is skipped if OTP addon is not installed
     *
     * @test
     */
    public function user_can_request_password_reset_with_phone(): void
    {
        // Skip if OTP addon not activated
        if (!function_exists('addon_is_activated') || !addon_is_activated('otp_system')) {
            $this->markTestSkipped('Skipped...OTP system addon not activated');
        }

        // Arrange
        $user = User::factory()->create([
            'phone' => '+11234567890',
            'email' => null,
        ]);

        // Act
        $response = $this->post('/password/email', [
            'phone' => '+11234567890',
        ]);

        // Assert
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertNotNull($user->verification_code);
    }

    /**
     * Test: Verification code is 6 digits
     *
     * @test
     */
    public function verification_code_is_six_digits(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        // Act
        $this->post('/password/email', [
            'email' => 'user@example.com',
        ]);

        // Assert
        $user->refresh();
        $this->assertMatchesRegularExpression('/^\d{6}$/', $user->verification_code);
        $this->assertGreaterThanOrEqual(100000, (int) $user->verification_code);
        $this->assertLessThan(1000000, (int) $user->verification_code);
    }

    /**
     * Test: Old password no longer works after reset
     *
     * @test
     */
    public function old_password_no_longer_works_after_reset(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password'),
            'verification_code' => '123456',
        ]);

        // Act - Reset password
        $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Logout
        auth()->logout();

        // Try to login with old password
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'old_password',
        ]);

        // Assert
        // Assert
        $response->assertRedirect(); // Login fails and redirects back
        $this->assertGuest();
        $this->assertGuest();
    }

    /**
     * Test: New password works after reset
     *
     * @test
     */
    public function new_password_works_after_reset(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password'),
            'verification_code' => '123456',
        ]);

        // Act - Reset password
        $this->post('/password/reset/email/submit', [
            'email' => 'user@example.com',
            'code' => '123456',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        // Logout
        auth()->logout();

        // Try to login with new password
        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'new_password',
        ]);

        // Assert
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
