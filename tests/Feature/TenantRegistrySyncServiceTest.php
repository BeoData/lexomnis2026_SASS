<?php

namespace Tests\Feature;

use App\Services\TenantAppApiService;
use App\Services\TenantRegistrySyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TenantRegistrySyncServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_by_main_id_is_idempotent_and_encrypts_local_password(): void
    {
        $api = Mockery::mock(TenantAppApiService::class);
        $api->shouldReceive('getTenant')->twice()->with(42)->andReturn([
            'success' => true,
            'data' => [
                'id' => 42,
                'name' => 'Synced Firm',
                'slug' => 'synced-firm',
                'status' => 'active',
            ],
        ]);
        $api->shouldReceive('getTenantCredentials')->twice()->with(42)->andReturn([
            'success' => true,
            'data' => [
                'success' => true,
                'data' => [
                    'db_driver' => 'sqlite',
                    'db_host' => '',
                    'db_port' => '',
                    'db_name' => 'lexomnis_synced-firm',
                    'db_user' => '',
                    'db_password' => '',
                ],
            ],
        ]);

        $sync = new TenantRegistrySyncService($api);
        $first = $sync->syncByMainId(42);
        $second = $sync->syncByMainId(42);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(42, $second->main_firm_id);
        $this->assertSame('synced-firm', $second->tenant_key);
        $this->assertSame('', $second->decrypted_password);
        $this->assertSame('synced', $second->sync_status);
        $this->assertDatabaseCount('tenants', 1);
    }
}
