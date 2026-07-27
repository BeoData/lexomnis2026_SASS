<?php

namespace Tests\Unit\Services;

use App\Services\TenantAppApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class TenantAppApiServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_tenant_credentials_uses_protected_main_endpoint(): void
    {
        Log::spy();

        config([
            'services.tenant_app.url' => 'https://tenant-app.example.test',
            'services.tenant_app.api_path_prefix' => 'api/admin',
            'services.tenant_app.api_token' => 'test-api-token',
            'services.tenant_app.retry_attempts' => 1,
        ]);

        Http::fake([
            'https://tenant-app.example.test/api/admin/tenants/42/credentials' => Http::response([
                'success' => true,
                'data' => [
                    'db_driver' => 'mysql',
                    'db_host' => 'main-db.example.test',
                    'db_port' => '3306',
                    'db_name' => 'tenant_42',
                    'db_user' => 'tenant_42',
                    'db_password' => 'secret',
                ],
            ]),
        ]);

        $response = app(TenantAppApiService::class)->getTenantCredentials(42);

        $this->assertTrue($response['success']);
        $this->assertSame('tenant_42', $response['data']['data']['db_name']);

        Http::assertSent(fn ($request) => $request->method() === 'GET'
            && $request->url() === 'https://tenant-app.example.test/api/admin/tenants/42/credentials'
            && $request->hasHeader('Authorization', 'Bearer test-api-token'));

        Log::shouldHaveReceived('info')->with(
            'TenantAppApiService response',
            Mockery::on(fn (array $context) => data_get($context, 'body.data.db_password') === '[REDACTED]'
                && ! str_contains(json_encode($context), 'secret'))
        );
    }
}
