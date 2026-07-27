<?php

namespace Tests\Feature\Console;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class TenantsDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_hard_delete_with_database_drop_aborts_when_main_credentials_cannot_be_verified(): void
    {
        $tenant = Tenant::create([
            'tenant_key' => 'tenant-delete-test',
            'db_driver' => 'mysql',
            'db_host' => 'stale-db.example.test',
            'db_port' => '3306',
            'db_name' => 'stale_database_name',
            'db_user' => 'stale_user',
            'db_password' => Crypt::encryptString('stale-secret'),
            'active' => true,
        ]);

        $apiService = Mockery::mock(TenantAppApiService::class);
        $apiService->shouldReceive('getTenantCredentials')
            ->once()
            ->with($tenant->id)
            ->andReturn([
                'success' => false,
                'error' => 'main app unavailable',
                'status' => 503,
            ]);
        $this->app->instance(TenantAppApiService::class, $apiService);

        $this->artisan('tenants:delete', [
            'tenant_key' => $tenant->tenant_key,
            '--mode' => 'hard',
            '--drop-db' => true,
            '--force' => true,
        ])->expectsOutput('Unable to verify current tenant database target from main app. Hard delete aborted.')
            ->assertFailed();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
        $this->assertDatabaseCount('tenants_archive', 0);
    }

    public function test_failed_mandatory_backup_aborts_hard_delete(): void
    {
        $tenant = Tenant::create([
            'main_firm_id' => 42,
            'tenant_key' => 'backup-failure',
            'db_driver' => 'sqlite',
            'db_host' => '',
            'db_port' => '',
            'db_name' => 'backup-failure',
            'db_user' => '',
            'db_password' => Crypt::encryptString(''),
            'active' => true,
        ]);

        $apiService = Mockery::mock(TenantAppApiService::class);
        $apiService->shouldReceive('getTenantCredentials')->twice()->with(42)->andReturn([
            'success' => true,
            'data' => ['success' => true, 'data' => [
                'db_driver' => 'sqlite',
                'db_host' => '',
                'db_port' => '',
                'db_name' => 'backup-failure',
                'db_user' => '',
                'db_password' => '',
            ]],
        ]);
        $apiService->shouldReceive('backupTenantDatabase')->once()->with(42)->andReturn([
            'success' => false,
            'error' => 'backup unavailable',
        ]);
        $apiService->shouldNotReceive('deleteTenant');
        $apiService->shouldNotReceive('deleteTenantDatabase');
        $this->app->instance(TenantAppApiService::class, $apiService);

        $this->artisan('tenants:delete', [
            'tenant_key' => $tenant->tenant_key,
            '--mode' => 'hard',
            '--drop-db' => true,
            '--force' => true,
        ])->expectsOutput('Backup failed. Hard delete aborted.')
            ->assertFailed();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
        $this->assertDatabaseCount('tenants_archive', 0);
    }

    public function test_failed_archive_aborts_before_main_delete(): void
    {
        $tenant = Tenant::create([
            'main_firm_id' => 43,
            'tenant_key' => 'archive-failure',
            'db_driver' => 'sqlite',
            'db_host' => '',
            'db_port' => '',
            'db_name' => 'archive-failure',
            'db_user' => '',
            'db_password' => Crypt::encryptString(''),
            'active' => true,
        ]);

        $apiService = Mockery::mock(TenantAppApiService::class);
        $apiService->shouldReceive('getTenantCredentials')->once()->with(43)->andReturn([
            'success' => true,
            'data' => ['success' => true, 'data' => [
                'db_driver' => 'sqlite',
                'db_host' => '',
                'db_port' => '',
                'db_name' => 'archive-failure',
                'db_user' => '',
                'db_password' => '',
            ]],
        ]);
        $apiService->shouldReceive('backupTenantDatabase')->once()->with(43)->andReturn([
            'success' => true,
            'data' => ['success' => true],
        ]);
        $apiService->shouldNotReceive('deleteTenant');
        $this->app->instance(TenantAppApiService::class, $apiService);

        Schema::drop('tenants_archive');

        $this->artisan('tenants:delete', [
            'tenant_key' => $tenant->tenant_key,
            '--mode' => 'hard',
            '--force' => true,
        ])->expectsOutput('Failed to archive tenant. Hard delete aborted.')
            ->assertFailed();

        $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
    }
}
