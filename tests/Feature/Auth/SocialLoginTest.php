<?php

namespace Tests\Feature\Auth;

use App\Enums\SocialProvider;
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
        // Arrange - Mock Socialite
        $socialiteUser = $this->createSocialUser([
            'id' => 'google-123456',
            'email' => 'newuser@gmail.com',
            'name' => 'John Doe',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $this->mockSocialite('google', $socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@gmail.com',
            'provider' => 'google',
            'provider_id' => 'google-123456',
            'user_type' => \App\Enums\UserType::CUSTOMER->value,
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
        $socialiteUser = $this->createSocialUser([
            'id' => 'google-789',
            'email' => 'existing@gmail.com',
            'name' => 'John Doe',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $this->mockSocialite('google', $socialiteUser);

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
        $socialiteUser = $this->createSocialUser([
            'id' => 'fb-123456',
            'email' => 'user@facebook.com',
            'name' => 'Jane Smith',
            'avatar' => 'https://facebook.com/avatar.jpg',
        ]);

        $this->mockSocialite('facebook', $socialiteUser);

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
        $socialiteUser = $this->createSocialUser([
            'id' => 'twitter-123',
            'email' => 'user@twitter.com',
            'name' => 'Twitter User',
            'nickname' => 'twitteruser',
            'avatar' => 'https://twitter.com/avatar.jpg',
        ]);

        $this->mockSocialite('twitter', $socialiteUser);

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
        $socialiteUser = $this->createSocialUser([
            'id' => 'apple-xyz',
            'email' => 'user@privaterelay.appleid.com',
            'name' => 'Apple User',
        ]);

        $this->mockSocialite('sign-in-with-apple', $socialiteUser);

        // Act - Apple uses POST callback
        $response = $this->post('/apple-callback');

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

        $socialiteUser = $this->createSocialUser([
            'id' => 'google-auto',
            'email' => 'auto@gmail.com',
            'name' => 'Auto Verify',
        ]);

        $this->mockSocialite('google', $socialiteUser);

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
        $socialiteUser = $this->createSocialUser([
            'id' => 'no-email-123',
            'email' => null,
            'name' => 'No Email User',
        ]);

        $this->mockSocialite('google', $socialiteUser);

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
        $existingUser = User::factory()->socialAuth(SocialProvider::GOOGLE, 'google-repeat')->create([
            'email' => 'repeat@gmail.com',
        ]);

        // Mock Socialite returning same user
        $socialiteUser = $this->createSocialUser([
            'id' => 'google-repeat',
            'email' => 'repeat@gmail.com',
            'name' => 'Repeat User',
        ]);

        $this->mockSocialite('google', $socialiteUser);

        $initialCount = User::count();
        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert
        $this->assertAuthenticatedAs($existingUser);

        // Should not create duplicate user
        $this->assertDatabaseCount('users', $initialCount);
    }

    /**
     * Test: Social login redirects to appropriate dashboard
     *
     * @test
     */
    public function social_login_redirects_customer_to_home(): void
    {
        // Arrange
        $socialiteUser = $this->createSocialUser([
            'id' => 'redirect-test',
            'email' => 'redirect@gmail.com',
            'name' => 'Redirect Test',
        ]);

        $this->mockSocialite('google', $socialiteUser);

        // Act
        $response = $this->get('/social-login/google/callback');

        // Assert - Customer should redirect to home page
        $user = User::where('email', 'redirect@gmail.com')->first();
        $response->assertRedirect($user->homePage());
    }

    /**
     * Helper to create a Socialite User object with properties
     */
    protected function createSocialUser(array $attributes)
    {
        $user = new SocialiteUser;
        $user->id = $attributes['id'] ?? null;
        $user->name = $attributes['name'] ?? null;
        $user->email = $attributes['email'] ?? null;
        $user->token = 'test-token';
        $user->avatar = $attributes['avatar'] ?? null;
        $user->nickname = $attributes['nickname'] ?? null;

        return $user;
    }

    /**
     * Helper to mock Socialite driver
     */
    protected function mockSocialite(string $driver, $userObject)
    {
        $abstractProvider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        if ($driver === 'twitter' || $driver === 'sign-in-with-apple') {
            // Twitter uses OAuth1 without stateless, Apple handled separately without stateless
            $abstractProvider->shouldReceive('user')->andReturn($userObject);
        } else {
            $abstractProvider->shouldReceive('stateless')->andReturnSelf();
            $abstractProvider->shouldReceive('user')->andReturn($userObject);
        }
        Socialite::shouldReceive('driver')->with($driver)->andReturn($abstractProvider);
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
