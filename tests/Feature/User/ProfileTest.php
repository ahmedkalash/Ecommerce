<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $country = new \App\Models\Country;
        $country->name = 'United States';
        $country->code = 'US';
        $country->status = 1;
        $country->save();
    }

    /** @test */
    public function user_can_view_profile_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertViewIs('frontend.user.profile');
    }

    /** @test */
    public function user_can_update_profile_information()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'phone' => '123456789',
        ]);

        $response = $this->actingAs($user)->post(route('user.profile.update'), [
            'name' => 'New Name',
            'phone' => '987654321',
            'address' => '123 New St',
            'city' => 'Anytown',
            'country' => 'CountryName',
            'postal_code' => '12345',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('flash_notification');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'phone' => '987654321',
            'address' => '123 New St',
            'city' => 'Anytown',
            'postal_code' => '12345',
        ]);
    }

    /** @test */
    public function profile_update_requires_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('user.profile.update'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function user_can_update_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('user.password.update'), [
            'current_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('flash_notification');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /** @test */
    public function password_update_fails_with_incorrect_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('user.password.update'), [
            'current_password' => 'wrong-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }

    /** @test */
    public function password_update_fails_with_mismatched_confirmation()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->post(route('user.password.update'), [
            'current_password' => 'old-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'mismatch-password',
        ]);

        $response->assertSessionHasErrors('new_password');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
