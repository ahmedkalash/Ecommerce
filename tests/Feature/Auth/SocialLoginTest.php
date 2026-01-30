<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;

/**
 * Social Login Feature Tests
 *
 * Tests OAuth authentication flows including:
 * - Google, Facebook, Twitter, Apple login
 * - New user creation from social auth
 * - Linking social account to existing email
 * - Auto email verification for social users
 */
class SocialLoginTest extends AuthTestCase
{
    /**
     * Test: Google OAuth creates new customer user
     *
     * @test
     */
    public function google_oauth_creates_new_customer_user(): void
    {
        // Arrange - Mock Socialite
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-123456');
        $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@gmail.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@gmail.com',
            'provider' => 'google',
            'provider_id' => 'google-123456',
            'user_type' => 'customer',
        ]);

        $user = User::where('email', 'newuser@gmail.com')->first();
        $this->assertNotNull($user);
        $this->assertUserVerified($user); // Social login auto-verifies
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Google login links to existing email
     *
     * @test
     */
    public function google_login_links_to_existing_email(): void
    {
        // Arrange - Create existing user
        $existingUser = User::factory()->customer()->create([
            'email' => 'existing@gmail.com',
            'provider' => null,
            'provider_id' => null,
        ]);

        // Mock Socialite with same email
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-789');
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $socialiteUser->shouldReceive('getName')->andReturn('John Doe');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert - User should be updated with provider info
        $existingUser->refresh();
        $this->assertEquals('google', $existingUser->provider);
        $this->assertEquals('google-789', $existingUser->provider_id);
        $this->assertAuthenticatedAs($existingUser);
    }

    /**
     * Test: Facebook login works
     *
     * @test
     */
    public function facebook_login_creates_new_user(): void
    {
        // Arrange
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('fb-123456');
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@facebook.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Smith');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://facebook.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/facebook/callback');

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'user@facebook.com',
            'provider' => 'facebook',
            'provider_id' => 'fb-123456',
        ]);

        $user = User::where('email', 'user@facebook.com')->first();
        $this->assertUserVerified($user);
    }

    /**
     * Test: Twitter login works
     *
     * @test
     */
    public function twitter_login_creates_new_user(): void
    {
        // Arrange
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('twitter-123');
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@twitter.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Twitter User');
        $socialiteUser->shouldReceive('getNickname')->andReturn('twitteruser');
        $socialiteUser->shouldReceive('getAvatar')->andReturn('https://twitter.com/avatar.jpg');

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/twitter/callback');

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'user@twitter.com',
            'provider' => 'twitter',
            'provider_id' => 'twitter-123',
        ]);
    }

    /**
     * Test: Apple callback works (POST method)
     *
     * @test
     */
    public function apple_callback_creates_new_user(): void
    {
        // Arrange
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('apple-xyz');
        $socialiteUser->shouldReceive('getEmail')->andReturn('user@privaterelay.appleid.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Apple User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act - Apple uses POST callback
        $response = $this->post('/social-login/apple/callback');

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'user@privaterelay.appleid.com',
            'provider' => 'apple',
            'provider_id' => 'apple-xyz',
        ]);
    }

    /**
     * Test: Social login auto-verifies email
     *
     * @test
     */
    public function social_login_auto_verifies_email(): void
    {
        // Arrange
        $this->enableEmailVerification();

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-auto');
        $socialiteUser->shouldReceive('getEmail')->andReturn('auto@gmail.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Auto Verify');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert
        $user = User::where('email', 'auto@gmail.com')->first();
        $this->assertNotNull($user);

        // Social login should auto-verify even when email verification is enabled
        $this->assertUserVerified($user);
    }

    /**
     * Test: Social login without email creates user with provider ID
     *
     * @test
     */
    public function social_login_without_email_uses_provider_id(): void
    {
        // Arrange - Some providers might not return email
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('no-email-123');
        $socialiteUser->shouldReceive('getEmail')->andReturn(null);
        $socialiteUser->shouldReceive('getName')->andReturn('No Email User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert - User should be created with provider_id even without email
        $this->assertDatabaseHas('users', [
            'provider' => 'google',
            'provider_id' => 'no-email-123',
            'email' => null,
        ]);
    }

    /**
     * Test: Existing social user can login again
     *
     * @test
     */
    public function existing_social_user_can_login_again(): void
    {
        // Arrange - Create existing social user
        $existingUser = User::factory()->socialAuth('google', 'google-repeat')->create([
            'email' => 'repeat@gmail.com',
        ]);

        // Mock Socialite returning same user
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google-repeat');
        $socialiteUser->shouldReceive('getEmail')->andReturn('repeat@gmail.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Repeat User');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert
        $this->assertAuthenticatedAs($existingUser);

        // Should not create duplicate user
        $this->assertDatabaseCount('users', 2); // 1 existing + 1 admin from setUp
    }

    /**
     * Test: Social login redirects to appropriate dashboard
     *
     * @test
     */
    public function social_login_redirects_customer_to_home(): void
    {
        // Arrange
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('redirect-test');
        $socialiteUser->shouldReceive('getEmail')->andReturn('redirect@gmail.com');
        $socialiteUser->shouldReceive('getName')->andReturn('Redirect Test');
        $socialiteUser->shouldReceive('getAvatar')->andReturn(null);

        Socialite::shouldReceive('driver->user')->andReturn($socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert - Customer should redirect to home
        $response->assertRedirect('/');
    }

    /**
     * Clean up Mockery after each test
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
