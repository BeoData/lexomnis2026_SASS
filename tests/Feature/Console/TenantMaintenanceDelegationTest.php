<?php

namespace Tests\Feature\Console;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Mockery;
use Tests\TestCase;

class TenantMaintenanceDelegationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sqlite_backup_and_migration_are_delegated_to_main_app(): void
    {
        $tenant = Tenant::create([
            'main_firm_id' => 42,
            'tenant_key' => 'sqlite-tenant',
            'db_driver' => 'sqlite',
            'db_host' => '',
            'db_port' => '',
            'db_name' => 'lexomnis_sqlite-tenant',
            'db_user' => '',
            'db_password' => Crypt::encryptString(''),
            'active' => true,
        ]);

        $credentials = [
            'success' => true,
            'data' => [
                'success' => true,
                'data' => [
                    'db_driver' => 'sqlite',
                    'db_host' => '',
                    'db_port' => '',
                    'db_name' => 'lexomnis_sqlite-tenant',
                    'db_user' => '',
                    'db_password' => '',
                ],
            ],
        ];

        $api = Mockery::mock(TenantAppApiService::class);
        $api->shouldReceive('getTenantCredentials')->twice()->with(42)->andReturn($credentials);
        $api->shouldReceive('backupTenantDatabase')->once()->with(42)->andReturn([
            'success' => true,
            'data' => ['success' => true, 'data' => ['filename' => 'backup.sqlite']],
        ]);
        $api->shouldReceive('migrateTenantDatabase')->once()->with(42)->andReturn([
            'success' => true,
            'data' => ['success' => true],
        ]);
        $this->app->instance(TenantAppApiService::class, $api);

        $this->artisan('tenants:backup', ['--tenant' => $tenant->id])
            ->expectsOutputToContain('SQLite backup completed in main app')
            ->assertSuccessful();

        $this->artisan('tenants:migrate', ['--tenant' => $tenant->id])
            ->expectsOutputToContain('OK: sqlite-tenant')
            ->assertSuccessful();
    }
}
