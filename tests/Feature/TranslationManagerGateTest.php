<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Feature tests for the 'use-translation-manager' authorization gate.
 *
 * Verifies that:
 * - The gate is registered
 * - Admin/staff users are allowed
 * - Non-admin users are denied
 * - Guest (null) users are denied
 */
class TranslationManagerGateTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test that the gate is defined.
     */
    public function test_gate_is_defined(): void
    {
        $this->assertTrue(
            Gate::has('use-translation-manager'),
            'The use-translation-manager gate should be defined'
        );
    }

    /**
     * Test that an admin user is allowed through the gate.
     */
    public function test_admin_user_is_allowed(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'user_type' => 'admin',
        ]);

        $this->assertTrue(
            Gate::forUser($user)->allows('use-translation-manager'),
            'An admin user should be allowed to use the translation manager'
        );
    }

    /**
     * Test that a staff user is allowed through the gate.
     */
    public function test_staff_user_is_allowed(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'user_type' => 'staff',
        ]);

        $this->assertTrue(
            Gate::forUser($user)->allows('use-translation-manager'),
            'A staff user should be allowed to use the translation manager'
        );
    }

    /**
     * Test that a regular customer user is denied through the gate.
     */
    public function test_customer_user_is_denied(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'user_type' => 'customer',
        ]);

        $this->assertFalse(
            Gate::forUser($user)->allows('use-translation-manager'),
            'A customer user should NOT be allowed to use the translation manager'
        );
    }

    /**
     * Test that a guest (null) user is denied through the gate.
     */
    public function test_guest_is_denied(): void
    {
        $result = Gate::forUser(null)->allows('use-translation-manager');

        $this->assertFalse(
            $result,
            'A guest (null user) should NOT be allowed to use the translation manager'
        );
    }
}
