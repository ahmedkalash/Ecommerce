<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Mail\MailManager;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Registration Feature Tests
 *
 * Tests the complete customer registration flow including
 * - Basic registration with email
 * - Registration validation
 * - Duplicate email/phone detection
 * - Cart transfer during registration
 * - Email verification triggering
 */
class RegistrationTest extends AuthTestCase
{
    // Todo: test response status code and error messages

    /**
     * Test: Customer can register with a valid email and password
     *
     * @test
     */
    public function customer_can_register_with_valid_email_and_password(): void
    {
        // Arrange
        $this->disableEmailVerification(); // Simplify for basic test Todo: add test for email verification
        $this->disableRegistrationVerify(); // Simplify for basic test Todo: add test for pre email verification
        $use_data = User::factory()->raw([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Act
        $response = $this->post('/register', $use_data);

        // Assert
        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => $use_data['email'],
            'name' => $use_data['name'],
            'user_type' => UserType::CUSTOMER,
        ]);

        $user = User::where('email', $use_data['email'])->first();
        Hash::check('password123', $user->password);

        // Assert User Authenticated
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test: Registration fails with duplicate email
     *
     * @test
     */
    public function registration_fails_with_duplicate_email(): void
    {
        // Arrange
        $email = 'existing@example.com';
        User::factory()->create(['email' => $email]);

        $use_data = User::factory()->raw([
            'name' => 'Jane Doe',
            'email' => $email,
            'phone' => null,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Act
        $response = $this->post('/register', $use_data);
        // Todo: test response status code and error messages

        // Assert
        $count = User::where('email', $email)->count();
        $this->assertEquals(1, $count);

        // Now the controller should calls flash() and returns back()
        $this->assertGuest();
    }

    /**
     * Test: Registration fails with a duplicate phone
     *
     * @test
     */
    public function registration_fails_with_duplicate_phone(): void
    {
        // Arrange
        $phone = '+11234567890';
        User::factory()->create(['phone' => $phone]);

        $use_data = User::factory()->raw([
            'name' => 'Jane Doe',
            'country_code' => '1',
            'phone' => $phone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'email' => null, // needed for testing phone registration
        ]);

        // Act
        $response = $this->post('/register', $use_data);
        // Todo: test response status code and error messages

        // Assert
        $count = User::where('phone', $phone)->count();
        $this->assertEquals(1, $count);
        // Now the controller should calls flash() and returns back()
        $this->assertGuest();
    }

    /**
     * Test: Registration requires password confirmation
     *
     * @test
     */
    public function registration_requires_password_confirmation(): void
    {
        // Arrange
        $use_data = User::factory()->raw([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ]);

        // Act
        $response = $this->post('/register', $use_data);

        // Assert
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertGuest();
    }

    /**
     * Test: Registration requires a name
     *
     * @test
     */
    public function registration_requires_name(): void
    {
        // Arrange
        $use_data = User::factory()->raw([
            'name' => null,
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Act
        $response = $this->post('/register', $use_data);

        // Assert
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', [
            'email' => 'john@example.com',
        ]);
    }

    /**
     * Test: Registration requires a minimum password length
     *
     * @test
     */
    public function registration_requires_minimum_password_length(): void
    {
        // Arrange
        $use_data = User::factory()->raw([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345', // Less than 6 chars
            'password_confirmation' => '12345',
        ]);

        // Act
        $response = $this->post('/register', $use_data);

        // Assert
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => $use_data['email']]);
    }

    /**
     * Test: Guest cart items transfer to the user cart after registration
     *
     * @test
     */
    public function guest_cart_items_transfer_to_user_cart_after_registration(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $this->disableRegistrationVerify();

        $use_data = User::factory()->raw([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Create a guest cart with temp_user_id
        $cart = [
            [
                'product_id' => Product::factory()->create()->id,
                'quantity' => 2,
            ],
            [
                'product_id' => Product::factory()->create()->id,
                'quantity' => 5,
            ],
        ];
        $tempUserId = $this->createGuestCart($cart);

        // Act
        $response = $this->post('/register', $use_data);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // Cart transferred
        $this->assertCartTransferred($tempUserId, $user->id);

        // Verify cart items count
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $cart[0]['product_id'],
            'quantity' => $cart[0]['quantity'],
        ]);
        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $cart[1]['product_id'],
            'quantity' => $cart[1]['quantity'],
        ]);
    }

    /**
     * Test: Registration triggers email verification when enabled
     *
     * @test
     */
    public function registration_triggers_email_verification_when_enabled(): void
    {
        // Arrange
        $this->enableEmailVerification();
        $this->disableRegistrationVerify();

        // Create an admin user for email utilities that need get_admin()
        User::factory()->admin()->create([
            'email' => 'admin@test.com',
            'name' => 'Test Admin',
        ]);

        // Act
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        // Email should NOT be verified yet
        $this->assertUserNotVerified($user);

        // Verification email should be sent
        // Assert: Check that the verification email was sent to the correct user
        Mail::assertQueued(MailManager::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test: Registration auto-verifies email when verification disabled
     *
     * @test
     */
    public function registration_auto_verifies_email_when_verification_disabled(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $this->disableRegistrationVerify();

        // Act
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // Email should be auto-verified
        $this->assertUserVerified($user);
    }

    /**
     * Test: Registration stores referral code from a cookie
     *
     * @test
     */
    public function registration_stores_referral_code_from_cookie(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $this->disableRegistrationVerify();

        $referrer = User::factory()->create([
            'referral_code' => 'REFER123',
        ]);

        // Act - Use withUnencryptedCookie for Cookie::get() to work
        $response = $this->withUnencryptedCookie('referral_code', 'REFER123')
            ->post('/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);
        $this->assertEquals($referrer->id, $user->referred_by);
    }

    /**
     * Test: User redirects to home after successful registration
     *
     * @test
     */
    public function user_redirects_to_home_after_successful_registration(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/');
    }

    /**
     * Test: User redirects to saved session link after registration
     *
     * @test
     */
    public function user_redirects_to_saved_session_link_after_registration(): void
    {
        // Arrange
        $this->disableEmailVerification();
        session(['link' => '/checkout']);

        // Act
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/checkout');
    }

    /**
     * Test: Invalid email format is handled gracefully
     *
     * Note: The controller uses filter_var() to check if input is email.
     * If not, it treats it as phone registration. Without OTP system,
     * this will fail gracefully.
     *
     * @test
     */
    public function registration_handles_invalid_email_gracefully(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $this->disableRegistrationVerify();

        // Act - Send invalid email format (will be treated as phone)
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert - Should not crash, and should not create user with invalid data
        // Todo: test response status code and error messages
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
        $this->assertGuest();
    }

    /**
     * Test: Registration creates customer user type by default
     *
     * @test
     */
    public function registration_creates_customer_user_type_by_default(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->user_type);
    }
}
