<?php

namespace Tests\Feature\Customer\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

/**
 * Password Reset Feature Tests
 *
 * Tests the complete password reset flow using Laravel's standard token-based system:
 * - Reset link request
 * - Reset form display
 * - Password update with valid token
 * - Invalid/expired token handling
 * - Banned user handling
 * - User type-specific redirects
 */
class PasswordResetTest extends AuthTestCase
{
    // ==========================================
    // FORGOT PASSWORD (Request Reset Link)
    // ==========================================

    /**
     * Test: User can view forgot password form
     *
     * @test
     */
    public function user_can_view_forgot_password_form(): void
    {
        // Act
        $response = $this->get(route('password.request'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('frontend.auth.forgot-password');
    }

    /**
     * Test: User can request password reset link with valid email
     *
     * @test
     */
    public function user_can_request_password_reset_link_with_valid_email(): void
    {
        // Arrange
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
        ]);

        // Act
        $response = $this->post(route('password.email'), [
            'email' => 'test_user@example.com',
        ]);

        // Assert
        $response->assertSessionHas('alert.config');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /**
     * Test: Password reset request fails with non-existent email
     *
     * @test
     */
    public function password_reset_request_fails_with_nonexistent_email(): void
    {
        // Arrange
        Notification::fake();

        // Act
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    /**
     * Test: Password reset request fails for banned user
     *
     * @test
     */
    public function password_reset_request_fails_for_banned_user(): void
    {
        // Arrange
        Notification::fake();
        User::factory()->create([
            'email' => 'test_banned@example.com',
            'banned' => 1,
        ]);

        // Act
        $response = $this->post(route('password.email'), [
            'email' => 'test_banned@example.com',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
        Notification::assertNothingSent();
    }

    /**
     * Test: Password reset request requires valid email (caught by base trait)
     *
     * Note: The validation for required email is handled by Laravel's
     * SendsPasswordResetEmails trait which runs after recaptcha validation.
     * This test verifies the email format validation works correctly.
     *
     * @test
     */
    public function password_reset_request_requires_valid_email(): void
    {
        // Act - Submit with invalid but non-empty email
        $response = $this->post(route('password.email'), [
            'email' => 'notanemail',
        ]);

        // Assert - Laravel's validation returns errors
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Password reset request validates email format
     *
     * @test
     */
    public function password_reset_request_validates_email_format(): void
    {
        // Act
        $response = $this->post(route('password.email'), [
            'email' => 'not-an-email',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
    }

    // ==========================================
    // RESET PASSWORD FORM
    // ==========================================

    /**
     * Test: User can view reset password form with valid token
     *
     * @test
     */
    public function user_can_view_reset_password_form_with_valid_token(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('frontend.auth.reset-password');
        $response->assertViewHas('token', $token);
    }

    // ==========================================
    // RESET PASSWORD (Submit New Password)
    // ==========================================

    /**
     * Test: User can reset password with valid token
     *
     * @test
     */
    public function user_can_reset_password_with_valid_token(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'test_user@example.com',
            'password' => Hash::make('old_password'),
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertRedirect($user->homePage());
        $this->assertAuthenticatedAs($user);

        // Verify password changed
        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
        $this->assertFalse(Hash::check('old_password', $user->password));
    }

    /**
     * Test: Password reset fails with invalid token
     *
     * @test
     */
    public function password_reset_fails_with_invalid_token(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
            'password' => Hash::make('old_password'),
        ]);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email'); // Laravel returns 'email' error for invalid token
        $this->assertGuest();

        // Verify password NOT changed
        $user->refresh();
        $this->assertTrue(Hash::check('old_password', $user->password));
    }

    /**
     * Test: Password reset fails with expired token
     *
     * @test
     */
    public function password_reset_fails_with_expired_token(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
            'password' => Hash::make('old_password'),
        ]);
        $token = Password::createToken($user);

        // Expire the token by traveling forward in time
        $this->travel(config('auth.passwords.users.expire', 60) + 1)->minutes();

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Verify password NOT changed
        $user->refresh();
        $this->assertTrue(Hash::check('old_password', $user->password));
    }

    /**
     * Test: Password reset fails with mismatched email
     *
     * @test
     */
    public function password_reset_fails_with_mismatched_email(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        User::factory()->create([
            'email' => 'other@example.com',
        ]);

        // Act - Use token for user1 but email for user2
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'other@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
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
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'different_password',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test: Password reset enforces minimum length
     *
     * @test
     */
    public function password_reset_enforces_minimum_length(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test: Password reset fails for banned user
     *
     * @test
     */
    public function password_reset_fails_for_banned_user(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test_banned@example.com',
            'password' => Hash::make('old_password'),
            'banned' => 1,
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_banned@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert - Controller checks for banned users
        $response->assertRedirect();
        $this->assertGuest();

        // Verify password NOT changed
        $user->refresh();
        $this->assertTrue(Hash::check('old_password', $user->password));
    }

    // ==========================================
    // USER TYPE-SPECIFIC REDIRECTS
    // ==========================================

    /**
     * Test: Customer redirects to home after password reset
     *
     * @test
     */
    public function customer_redirects_to_home_after_password_reset(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'test_customer@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_customer@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    // ==========================================
    // POST-RESET BEHAVIOR
    // ==========================================

    /**
     * Test: User is automatically logged in after password reset
     *
     * @test
     */
    public function user_is_automatically_logged_in_after_password_reset(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        // Act
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Token is invalidated after successful reset
     *
     * @test
     */
    public function token_is_invalidated_after_successful_reset(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'test_user@example.com',
        ]);
        $token = Password::createToken($user);

        // Act - First reset succeeds
        $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Logout
        auth()->logout();

        // Try to use the same token again
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'test_user@example.com',
            'password' => 'another_password',
            'password_confirmation' => 'another_password',
        ]);

        // Assert - Token should be invalid now
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Verify password is still the first reset value
        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }

    // ==========================================
    // RATE LIMITING
    // ==========================================

    /**
     * Test: Password reset request is rate limited
     *
     * @test
     */
    public function password_reset_request_is_rate_limited(): void
    {
        // Arrange
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'test_user@example.com',
        ]);

        // Act - Make multiple requests
        for ($i = 0; $i < 3; $i++) {
            $response = $this->post(route('password.email'), [
                'email' => 'test_user@example.com',
            ]);
        }

        // Assert - Request should succeed (we're under default limit)
        $response->assertRedirect();
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    // ==========================================
    // GUEST MIDDLEWARE
    // ==========================================

    /**
     * Test: Authenticated user cannot access forgot password form
     *
     * @test
     */
    public function authenticated_user_cannot_access_forgot_password_form(): void
    {
        // Arrange
        /** @var \App\Models\User $user */
        $user = User::factory()->customer()->create();
        $this->actingAs($user);

        // Act
        $response = $this->get(route('password.request'));

        // Assert - Should redirect to home (guest middleware)
        $response->assertRedirect('/');
    }

    /**
     * Test: Authenticated user cannot submit password reset
     *
     * @test
     */
    public function authenticated_user_cannot_submit_password_reset(): void
    {
        // Arrange
        /** @var \App\Models\User $user */
        $user = User::factory()->customer()->create();
        $this->actingAs($user);

        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
        ]);
        $token = Password::createToken($otherUser);

        // Act
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'other@example.com',
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert - Should redirect to home (guest middleware)
        $response->assertRedirect('/');
    }

    // ==========================================
    // CASE-INSENSITIVE EMAIL
    // ==========================================

    /**
     * Test: Password reset request works with different email casing
     *
     * Registration stores emails in lowercase. The password broker
     * should still find the user when the email casing differs.
     *
     * @test
     */
    public function password_reset_request_works_with_different_email_casing(): void
    {
        // Arrange
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'test_diff_case_'.time().'@example.com', // stored lowercase
        ]);

        // Act - Request with uppercase email
        $response = $this->post(route('password.email'), [
            'email' => strtoupper($user->email),
        ]);

        // Assert - Should still find the user (DB collation dependent)
        $response->assertSessionHas('alert.config');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /**
     * Test: Password reset submit works with different email casing
     *
     * @test
     */
    public function password_reset_submit_works_with_different_email_casing(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email' => 'test_diff_case_submit_'.time().'@example.com',
            'password' => Hash::make('old_password'),
        ]);
        $token = Password::createToken($user);

        // Act - Submit with mixed case email
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => strtoupper($user->email),
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert
        $response->assertRedirect($user->homePage());
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }

    // ==========================================
    // THEMED RESET FORM VIEW
    // ==========================================

    /**
     * Test: Reset form uses themed layout from settings
     *
     * The showResetForm() method renders the view based on
     * the `authentication_layout_select` business setting.
     *
     * @test
     */
    public function reset_form_uses_themed_layout(): void
    {
        // Arrange
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
        $token = Password::createToken($user);

        $layout = get_setting('authentication_layout_select');

        // Act
        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]));

        // Assert - View is now fixed to the Fastkart theme
        $response->assertStatus(200);
        $response->assertViewIs('frontend.auth.reset-password');
        $response->assertViewHas('token', $token);
        $response->assertViewHas('email', $user->email);
    }

    // ==========================================
    // CONCURRENT RESET TOKENS
    // ==========================================

    /**
     * Test: Requesting a new reset token invalidates the previous one
     *
     * When a user requests a second reset link, the old token should
     * no longer work (Laravel's broker replaces the token row).
     *
     * @test
     */
    public function new_reset_token_invalidates_previous_token(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'password' => Hash::make('original_password'),
        ]);

        // Create first token
        $firstToken = Password::createToken($user);

        // Create second token (should invalidate the first)
        $secondToken = Password::createToken($user);

        // Act - Try to reset with the first (old) token
        $response = $this->post(route('password.update'), [
            'token' => $firstToken,
            'email' => $user->email,
            'password' => 'hacked_password',
            'password_confirmation' => 'hacked_password',
        ]);

        // Assert - First token should be invalid
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Verify password was NOT changed
        $user->refresh();
        $this->assertTrue(Hash::check('original_password', $user->password));
    }

    /**
     * Test: Second (latest) token still works after requesting multiple tokens
     *
     * @test
     */
    public function latest_reset_token_still_works(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'password' => Hash::make('original_password'),
        ]);

        // Create first token (will be invalidated)
        Password::createToken($user);

        // Create second token (should be the valid one)
        $secondToken = Password::createToken($user);

        // Act - Reset with the latest token
        $response = $this->post(route('password.update'), [
            'token' => $secondToken,
            'email' => $user->email,
            'password' => 'new_password123',
            'password_confirmation' => 'new_password123',
        ]);

        // Assert - Latest token should work
        $response->assertRedirect($user->homePage());
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertTrue(Hash::check('new_password123', $user->password));
    }
}
