<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Seed some basic settings
        Setting::set('app_name', 'Original Name', 'string', 'general');
        Setting::set('items_per_page', '15', 'integer', 'general');
    }

    public function test_it_can_display_settings_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/settings');

        $response->assertStatus(200);
        $response->assertViewIs('admin.settings.index');
        $response->assertViewHas('groups');
    }

    public function test_it_can_update_settings()
    {
        $appNameSetting = Setting::where('key', 'app_name')->first();
        $itemsSetting = Setting::where('key', 'items_per_page')->first();

        $settingsData = [
            'settings' => [
                $appNameSetting->id => [
                    'key' => 'app_name',
                    'value' => 'New App Name',
                ],
                $itemsSetting->id => [
                    'key' => 'items_per_page',
                    'value' => '50',
                ],
            ]
        ];

        $response = $this->actingAs($this->user)
            ->put('/settings', $settingsData);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('New App Name', Setting::getByKey('app_name'));
        $this->assertEquals('50', Setting::getByKey('items_per_page'));
    }

    public function test_it_can_create_new_settings_via_update()
    {
        // Simulate adding a 'new' setting from the form
        $settingsData = [
            'settings' => [
                'new_setting_key' => [
                    'key' => 'new_setting',
                    'value' => 'New Value',
                ],
            ]
        ];

        $response = $this->actingAs($this->user)
            ->put('/settings', $settingsData);

        $response->assertRedirect();

        $this->assertEquals('New Value', Setting::getByKey('new_setting'));
    }
}
