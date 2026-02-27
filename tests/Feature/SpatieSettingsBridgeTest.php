<?php

namespace Tests\Feature;

use App\Models\BusinessSetting;
use App\Settings\GeneralSettings;
use App\Settings\SpatieSettingsBridge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpatieSettingsBridgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_maps_general_setting_keys_to_spatie_settings()
    {
        // Set up the exact value in spatie settings manually or let it use the default
        $settings = app(GeneralSettings::class);
        $settings->site_name = 'Test Site Name';
        $settings->save();

        // The bridge should return 'Test Site Name'
        $this->assertEquals('Test Site Name', SpatieSettingsBridge::get('site_name'));

        // get_setting should use the bridge implicitly
        $this->assertEquals('Test Site Name', get_setting('site_name'));
    }

    public function test_get_setting_falls_back_to_database_for_unmapped_keys()
    {
        // Add a setting not mapped to Spatie
        $setting = new BusinessSetting;
        $setting->type = 'unmapped_setting_key';
        $setting->value = 'Old DB Value';
        $setting->save();

        $this->assertNull(SpatieSettingsBridge::get('unmapped_setting_key'));

        $this->assertEquals('Old DB Value', get_setting('unmapped_setting_key'));
    }
}
