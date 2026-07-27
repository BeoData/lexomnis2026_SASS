<?php

namespace Tests\Feature\Console;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Mockery;
use Tests\TestCase;

class TenantsVerifySyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reports_credential_drift_without_printing_passwords(): void
    {
        $tenant = Tenant::create([
            'tenant_key' => 'tenant-42',
            'db_driver' => 'mysql',
            'db_host' => 'local-db.example.test',
            'db_port' => '3306',
            'db_name' => 'tenant_42',
            'db_user' => 'tenant_42',
            'db_password' => Crypt::encryptString('local-secret'),
            'active' => true,
        ]);

        $apiService = Mockery::mock(TenantAppApiService::class);
        $apiService->shouldReceive('getTenantCredentials')
            ->once()
            ->with($tenant->id)
            ->andReturn([
                'success' => true,
                'data' => [
                    'success' => true,
                    'data' => [
                        'db_driver' => 'mysql',
                        'db_host' => 'main-db.example.test',
                        'db_port' => '3306',
                        'db_name' => 'tenant_42',
                        'db_user' => 'tenant_42',
                        'db_password' => 'main-secret',
                    ],
                ],
            ]);
        $this->app->instance(TenantAppApiService::class, $apiService);

        $this->artisan('tenants:verify-sync')
            ->expectsOutputToContain('db_host')
            ->expectsOutputToContain('MISMATCH')
            ->doesntExpectOutputToContain('local-secret')
            ->doesntExpectOutputToContain('main-secret')
            ->assertSuccessful();
    }
}
