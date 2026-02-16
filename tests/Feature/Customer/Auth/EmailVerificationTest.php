<?php

namespace Tests\Feature\Customer\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

/**
 * Email Verification Feature Tests
 *
 * Tests the security and logic of the email verification flow.
 */
class EmailVerificationTest extends AuthTestCase
{
    /**
     * Test: User can verify email with a valid signed verification link
     *
     * @test
     */
    public function user_can_verify_email_with_valid_signed_link(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create([
            'email' => 'user@example.com',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Act
        $response = $this->actingAs($user)->get($verificationUrl);

        // Assert - Controller redirects to intended or home
        $response->assertRedirect($user->homePage());

        $user->refresh();
        $this->assertUserVerified($user);
    }

    /**
     * Test: Email verification fails with invalid hash
     *
     * @test
     */
    public function email_verification_fails_with_invalid_hash(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create([
            'email' => 'user@example.com',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-hash')]
        );

        // Act
        $response = $this->actingAs($user)->get($verificationUrl);

        // Assert - Should fail signature/hash check
        $response->assertStatus(403);

        $user->refresh();
        $this->assertUserNotVerified($user);
    }

    /**
     * Test: Already verified user is redirected when clicking verification link
     *
     * @test
     */
    public function already_verified_user_redirected_from_verification_link(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email_verified_at' => now(),
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Act
        $response = $this->actingAs($user)->get($verificationUrl);

        // Assert
        $response->assertRedirect($user->homePage());
    }

    /**
     * Test: User can access verification notice page when unverified
     *
     * @test
     */
    public function unverified_user_can_access_verification_notice(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();

        // Act
        $response = $this->actingAs($user)->get(route('verification.notice'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('auth.'.get_setting('authentication_layout_select').'.verify_email');
    }

    /**
     * Test: Verified user is redirected from verification notice page
     *
     * @test
     */
    public function verified_user_redirected_from_verification_notice(): void
    {
        // Arrange
        $user = User::factory()->customer()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('verification.notice'));

        // Assert
        $response->assertRedirect($user->homePage());
    }

    /**
     * Test: User can resend verification email
     *
     * @test
     */
    public function user_can_resend_verification_email(): void
    {
        // Arrange
        Notification::fake();
        $user = User::factory()->customer()->unverified()->create();

        // Act
        $response = $this->actingAs($user)->get(route('verification.resend'));

        // Assert
        $response->assertRedirect();
        Notification::assertSentTo($user, EmailVerificationNotification::class);
    }

    /**
     * Test: Unverified user redirected from protected routes
     *
     * @test
     */
    public function unverified_user_redirected_from_protected_routes(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();
        $this->enableEmailVerification();

        // Act
        $response = $this->actingAs($user)->get(route('dashboard'));

        // Assert
        $response->assertRedirect(route('verification.notice'));
    }

    /**
     * Test: Guests cannot access verification routes
     *
     * @test
     */
    public function guests_cannot_access_verification_routes(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
        $this->get(route('verification.resend'))->assertRedirect(route('login'));
    }

    /**
     * Test: Verification link requires valid signature
     *
     * @test
     */
    public function verification_link_requires_valid_signature(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();

        $url = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        // Act - No signature in URL
        $response = $this->actingAs($user)->get($url);

        // Assert
        $response->assertStatus(403);
    }

    /**
     * Test: Expired verification link returns 403
     *
     * @test
     */
    public function expired_verification_link_fails(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();

        // Create a link that expired 1 minute ago
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Act
        $response = $this->actingAs($user)->get($verificationUrl);

        // Assert - Expired signature should fail
        $response->assertStatus(403);

        $user->refresh();
        $this->assertUserNotVerified($user);
    }

    /**
     * Test: User cannot verify another user's email
     *
     * @test
     */
    public function user_cannot_verify_another_users_email(): void
    {
        // Arrange
        $userA = User::factory()->customer()->unverified()->create();
        $userB = User::factory()->customer()->unverified()->create();

        // Create verification URL for User B
        $verificationUrlForB = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $userB->id, 'hash' => sha1($userB->getEmailForVerification())]
        );

        // Act - User A tries to use User B's verification link
        $response = $this->actingAs($userA)->get($verificationUrlForB);

        // Assert - Should fail (Laravel checks user ID match)
        $response->assertStatus(403);

        // Neither user should be verified
        $userA->refresh();
        $userB->refresh();
        $this->assertUserNotVerified($userA);
        $this->assertUserNotVerified($userB);
    }

    /**
     * Test: Already verified user cannot resend verification email
     *
     * @test
     */
    public function verified_user_cannot_resend_verification_email(): void
    {
        // Arrange
        Notification::fake();
        $user = User::factory()->customer()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this->actingAs($user)->get(route('verification.resend'));

        // Assert - Should redirect (HasNotVerifiedEmail middleware)
        $response->assertRedirect($user->homePage());

        // No notification should be sent
        Notification::assertNotSentTo($user, EmailVerificationNotification::class);
    }

    /**
     * Test: Resend verification is rate limited
     *
     * @test
     */
    public function resend_verification_is_rate_limited(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();

        // Act - Make 7 requests (limit is 6 per minute)
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)->get(route('verification.resend'));
        }

        // 7th request should be throttled
        $response = $this->actingAs($user)->get(route('verification.resend'));

        // Assert - Should be rate limited (429 Too Many Requests)
        $response->assertStatus(429);
    }

    /**
     * Test: User is redirected to intended URL after verification
     *
     * @test
     */
    public function user_redirected_to_intended_url_after_verification(): void
    {
        // Arrange
        $user = User::factory()->customer()->unverified()->create();
        $intendedUrl = route('dashboard');

        // Set an intended URL in the session
        $this->actingAs($user)->session(['url.intended' => $intendedUrl]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Act
        $response = $this->actingAs($user)->get($verificationUrl);

        // Assert - Should redirect to the intended URL
        $response->assertRedirect($intendedUrl);

        $user->refresh();
        $this->assertUserVerified($user);
    }
}
