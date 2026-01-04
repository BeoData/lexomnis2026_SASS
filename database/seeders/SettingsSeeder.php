<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // API Settings
        Setting::set(
            'tenant_app_url',
            env('TENANT_APP_URL', 'http://localhost:8000'),
            'string',
            'api',
            'Tenant App API Base URL',
            false
        );

        Setting::set(
            'tenant_app_api_token',
            env('TENANT_APP_API_TOKEN', ''),
            'string',
            'api',
            'API Token for Tenant App authentication (encrypted)',
            true
        );

        Setting::set(
            'tenant_app_timeout',
            env('TENANT_APP_TIMEOUT', '30'),
            'integer',
            'api',
            'API Request Timeout in seconds',
            false
        );

        Setting::set(
            'tenant_app_retry_attempts',
            env('TENANT_APP_RETRY_ATTEMPTS', '3'),
            'integer',
            'api',
            'Number of retry attempts for failed API requests',
            false
        );

        // General Settings
        Setting::set(
            'app_name',
            env('APP_NAME', 'LexOmnis Super Admin'),
            'string',
            'general',
            'Application Name',
            false
        );

        Setting::set(
            'items_per_page',
            '15',
            'integer',
            'general',
            'Number of items per page in lists',
            false
        );

        $this->command->info('Settings seeded successfully!');
    }
}

