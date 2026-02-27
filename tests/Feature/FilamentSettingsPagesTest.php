<?php

namespace Tests\Feature;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentSettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user to bypass authentication
        $user = User::factory()->create([
            'user_type' => 'admin',
            'password' => Hash::make('123456'),
        ]);

        // Usually staff record needed based on how auth is handled for admin panel in ecommerce
        // but we'll try to just act as a normal admin if that's sufficient or we bypass
        $this->actingAs($user, 'admin');
        // Note: the login might depend on multiple factors, but this is the simplest starting point
    }

    public function test_general_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageGeneralSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageGeneralSettings::class)
            ->assertSuccessful();
    }

    public function test_branding_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageBrandingSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageBrandingSettings::class)
            ->assertSuccessful();
    }

    public function test_social_auth_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageSocialAuthSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageSocialAuthSettings::class)
            ->assertSuccessful();
    }

    public function test_payment_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManagePaymentSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManagePaymentSettings::class)
            ->assertSuccessful();
    }

    public function test_shipping_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageShippingSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageShippingSettings::class)
            ->assertSuccessful();
    }

    public function test_seo_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageSeoSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageSeoSettings::class)
            ->assertSuccessful();
    }

    public function test_feature_toggle_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageFeatureToggleSettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageFeatureToggleSettings::class)
            ->assertSuccessful();
    }

    public function test_third_party_settings_page_renders()
    {
        $this->get(\App\Filament\Pages\Settings\ManageThirdPartySettings::getUrl())
            ->assertSuccessful();

        Livewire::test(\App\Filament\Pages\Settings\ManageThirdPartySettings::class)
            ->assertSuccessful();
    }
}
