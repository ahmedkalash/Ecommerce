<?php

namespace Tests\Feature\Auth;

use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Base Test Case for Authentication Feature Tests
 *
 * Provides common setup and helper methods for all auth-related tests.
 * Includes reCAPTCHA mocking, database reset, and external service mocking.
 */
abstract class AuthTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mock external services
        $this->mockRecaptcha();
        $this->mockMail();
        $this->mockQueue();
        $this->mockEvents();

        // Seed essential data if needed
        $this->seedEssentialData();
    }

    /**
     * Mock reCAPTCHA validation to always pass in tests.
     *
     * This prevents tests from failing due to missing reCAPTCHA tokens.
     */
    protected function mockRecaptcha(): void
    {
        $this->app->bind(Recaptcha::class, function () {
            return new class implements \Illuminate\Contracts\Validation\Rule
            {
                public function passes($attribute, $value): bool
                {
                    return true; // Always pass in tests
                }

                public function message(): string
                {
                    return 'The reCAPTCHA verification failed.';
                }
            };
        });
    }

    /**
     * Mock mail facade to intercept emails in tests.
     */
    protected function mockMail(): void
    {
        Mail::fake();
    }

    /**
     * Mock queue facade to intercept queued jobs.
     */
    protected function mockQueue(): void
    {
        Queue::fake();
    }

    /**
     * Mock event facade to intercept events.
     */
    protected function mockEvents(): void
    {
        Event::fake();
    }

    /**
     * Seed essential data for tests.
     *
     * This Seed essential data for tests to ensure business settings and other required data exist.
     */
    protected function seedEssentialData(): void
    {
        // Create an admin user for email utilities that need get_admin()
        //        \App\Models\User::firstOrCreate(
        //            ['email' => 'admin@test.com'],
        //            [
        //                'name' => 'Test Admin',
        //                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //                'user_type' => 'admin',
        //                'email_verified_at' => now(),
        //            ]
        //        );

        // Seed Email Template for password reset
        \App\Models\EmailTemplate::firstOrCreate(
            ['identifier' => 'password_reset_email_to_all'],
            [
                'subject' => 'Password Reset',
                'default_text' => 'Your password reset code is [[code]].',
                'status' => 1,
            ]
        );
    }

    /**
     * Disable reCAPTCHA via database settings.
     *
     * Useful for tests that need to verify reCAPTCHA is disabled.
     */
    protected function disableRecaptcha(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'google_recaptcha'],
            ['value' => 0]
        );
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'recaptcha_customer_register'],
            ['value' => 0]
        );
    }

    /**
     * Enable reCAPTCHA via database settings.
     */
    protected function enableRecaptcha(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'google_recaptcha'],
            ['value' => 1]
        );
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'recaptcha_customer_register'],
            ['value' => 1]
        );
    }

    /**
     * Disable email verification requirement.
     */
    protected function disableEmailVerification(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'email_verification'],
            ['value' => 0]
        );
    }

    /**
     * Enable email verification requirement.
     */
    protected function enableEmailVerification(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'email_verification'],
            ['value' => 1]
        );
    }

    /**
     * Disable pre-registration verification.
     */
    protected function disableRegistrationVerify(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'customer_registration_verify'],
            ['value' => 0]
        );
    }

    /**
     * Enable pre-registration verification.
     */
    protected function enableRegistrationVerify(): void
    {
        \DB::table('business_settings')->updateOrInsert(
            ['type' => 'customer_registration_verify'],
            ['value' => 1]
        );
    }

    /**
     * Assert that a user exists in the database with given attributes.
     */
    protected function assertUserExists(string $email): \App\Models\User
    {
        $user = \App\Models\User::where('email', $email)->first();
        $this->assertNotNull($user, "User with email {$email} not found");

        return $user;
    }

    /**
     * Assert that a user's email is verified.
     */
    protected function assertUserVerified(\App\Models\User $user): void
    {
        $user->refresh();
        $this->assertNotNull($user->email_verified_at, 'User email not verified');
    }

    /**
     * Assert that a user's email is not verified.
     */
    protected function assertUserNotVerified(\App\Models\User $user): void
    {
        $user->refresh();
        $this->assertNull($user->email_verified_at, 'User email should not be verified');
    }

    /**
     * Assert that guest cart items were transferred to user cart.
     */
    protected function assertCartTransferred(string $tempUserId, int $userId): void
    {
        $this->assertDatabaseMissing('carts', [
            'temp_user_id' => $tempUserId,
        ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $userId,
        ]);
    }

    /**
     * Create a fake session for guest cart.
     */
    protected function createGuestCart(array $items = []): string
    {
        $tempUserId = \Str::random(10);
        session(['temp_user_id' => $tempUserId]);

        // Create cart items with temp_user_id
        foreach ($items as $item) {
            \DB::table('carts')->insert(array_merge($item, [
                'temp_user_id' => $tempUserId,
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        return $tempUserId;
    }
}
