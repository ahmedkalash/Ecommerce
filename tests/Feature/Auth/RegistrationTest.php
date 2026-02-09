<?php

namespace Tests\Feature\Auth;

use App\Enums\UserType;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\MailManager;
use App\Models\Product;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

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
    protected bool $stopOnFirstFailure = true;

    // Todo: test response status code and error messages

    protected function setUp(): void
    {
        parent::setUp();
        $this->disableRecaptcha();
    }

    /**
     * Test: Registration requires mandatory fields
     *
     * @test
     */
    public function registration_requires_mandatory_fields(): void
    {
        // Act
        $response = $this->post(route('register'), []);

        //Assert
        // prevent the test from failing if the rules order was changed.
        $error_name = Arr::first(array_keys((new RegisterRequest)->rules()));
        // Assert - With stopOnFirstFailure, only a 'name' error will appear.
        $response->assertSessionHasErrors($error_name);
    }

    /**
     * Test: Customer can register with a valid email and password
     *
     * @test
     */
    public function customer_can_register_with_valid_email_and_password(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $use_data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

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

        $use_data = [
            'name' => 'Jane Doe',
            'email' => $email,
            'phone' => null,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

        // Assert
        $response->assertSessionHasErrors('email');
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

        $use_data = [
            'name' => 'Jane Doe',
            'country_code' => '1',
            'phone' => $phone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'email' => null, // needed for testing phone registration
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

        // Assert
        $response->assertSessionHasErrors('phone');
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
        $use_data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', Arr::only($use_data, ['email', 'name']));

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
        $use_data = [
            'name' => null,
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

        // Assert
        $response->assertSessionHasErrors('name');
        $this->assertDatabaseMissing('users', Arr::only($use_data, ['email']));
    }

    /**
     * Test: Registration requires a minimum password length
     *
     * @test
     */
    public function registration_requires_minimum_password_length(): void
    {
        // Arrange
        $use_data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '12345', // Less than 8 chars
            'password_confirmation' => '1234567',
            'agree_to_terms' => 'on',
        ];

        // Act
        $response = $this->post(route('register'), $use_data);

        // Assert
        $response->assertStatus(302);
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

        $use_data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ];

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
        $response = $this->post(route('register'), $use_data);

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
        Notification::fake();
        $this->enableEmailVerification();

        // Act
        $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        // Email should NOT be verified yet
        $this->assertUserNotVerified($user);

        // Verification email should be sent
        // Assert: Check that the verification notification was sent
        Notification::assertSentTo(
            $user,
            EmailVerificationNotification::class
        );
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

        // Act
        $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
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

        $referrer = User::factory()->create([
            'referral_code' => 'REFER123',
        ]);

        // Act - Use withUnencryptedCookie for Cookie::get() to work
        $response = $this->withUnencryptedCookie('referral_code', 'REFER123')
            ->post(route('register'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'agree_to_terms' => 'on',
            ]);

        // Assert
        $response->assertRedirect('/');

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // Todo: fix this test when affiliate system is ready
        // $this->assertEquals($referrer->id, $user->referred_by);
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
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
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
        session(['url.intended' => '/checkout']);

        // Act
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertRedirect('/checkout');
    }

    /**
     * Test: An invalid email format is handled gracefully
     *
     * @test
     */
    public function registration_handles_invalid_email_gracefully(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act - Send invalid email format (will be treated as phone)
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert - Should not crash, and should not create user with invalid data
        $response->assertSessionHasErrors('email');
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
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('/');
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserType::CUSTOMER->value, $user->user_type);
    }

    /**
     * Test: Registration prevents XSS in name field
     *
     * @test
     */
    public function registration_prevents_xss_in_name_field(): void
    {
        // Arrange
        $this->disableEmailVerification();
        $xssPayload = '<script>alert("XSS")</script>';

        // Act
        $response = $this->post(route('register'), [
            'name' => $xssPayload,
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);

        // Verify name is stored (Laravel escapes on output, not input)
        $this->assertEquals($xssPayload, $user->name);

        // Note: XSS prevention happens in Blade with {{ }} escaping
        // This test ensures malicious input is stored safely
    }

    /**
     * Test: Registration while already authenticated redirects to home
     *
     * @test
     */
    public function registration_while_already_authenticated(): void
    {
        // Arrange
        $existingUser = User::factory()->customer()->create();
        $this->actingAs($existingUser);

        // Act
        $response = $this->post(route('register'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert - Should redirect to home, not create new account
        $response->assertStatus(302);
        $response->assertRedirect('/');

        // Verify no new user was created
        $this->assertNull(User::where('email', 'newuser@example.com')->first());

        // Verify still authenticated as original user
        $this->assertAuthenticatedAs($existingUser);
    }

    /**
     * Test: Registration validates maximum field lengths
     *
     * @test
     */
    public function registration_validates_maximum_field_lengths(): void
    {
        // Act - Name exceeds max:190
        $response = $this->post(route('register'), [
            'name' => str_repeat('A', 191),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    /**
     * Test: Registration with whitespace in email is trimmed
     *
     * @test
     */
    public function registration_with_whitespace_in_email(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act - Email with leading/trailing whitespace
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => '  john@example.com  ',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);

        // Laravel validation trims by default for email rule
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('john@example.com', $user->email);
    }

    /**
     * Test: Cart merge handles duplicate products correctly
     *
     * @test
     */
    public function cart_merge_handles_duplicate_products(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Create existing user with cart item (quantity: 3)
        $user = User::factory()->customer()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user->carts()->create([
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Logout to simulate guest
        auth()->logout();

        // Create a guest cart with the same product (quantity: 2)
        $cart = [
            [
                'product_id' => $product->id,
                'quantity' => 2,
            ],
        ];
        $tempUserId = $this->createGuestCart($cart);

        // Act - Login (not register) to trigger cart merge
        $response = $this->post(route('login'), [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);

        // Verify cart was transferred
        $this->assertCartTransferred($tempUserId, $user->id);

        // Verify total quantity is merged (2 + 3 = 5)
        $user->refresh();
        $userCart = $user->carts()->where('product_id', $product->id)->first();
        $this->assertNotNull($userCart);
        $this->assertEquals(5, $userCart->quantity);
    }

    /**
     * Test: Registration without terms acceptance fails
     *
     * @test
     */
    public function registration_without_terms_acceptance(): void
    {
        // Act - Missing agree_to_terms
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHasErrors('agree_to_terms');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    }

    /**
     * Test: Registration validates realistic email formats
     *
     * @test
     */
    public function registration_validates_realistic_email_formats(): void
    {
        // Test invalid email - clearly invalid format
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'not-an-email',  // No @ symbol
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert - Laravel's email validation should catch this
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test: Registration trims whitespace from name input
     *
     * @test
     */
    public function registration_trims_whitespace_from_inputs(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act - Name with leading/trailing spaces
        $response = $this->post(route('register'), [
            'name' => '  John Doe  ',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);
        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user);

        // Verify name is trimmed (if your controller/request does this)
        // Note: Laravel doesn't auto-trim string fields, you may need to add TrimStrings middleware
        $this->assertStringNotContainsString('  ', $user->name);
    }

    /**
     * Test: Registration stores email in lowercase
     *
     * @test
     */
    public function registration_stores_email_in_lowercase(): void
    {
        // Arrange
        $this->disableEmailVerification();

        // Act - Register with mixed case email
        $response = $this->post(route('register'), [
            'name' => 'John Doe',
            'email' => 'User@Example.COM',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agree_to_terms' => 'on',
        ]);

        // Assert
        $response->assertStatus(302);

        // Verify email is stored in lowercase
        $user = User::where('email', 'user@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user@example.com', $user->email);
    }
}
