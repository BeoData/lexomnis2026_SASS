<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use App\Services\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class TenantsMigrate extends Command
{
    protected $signature = 'tenants:migrate {--tenant=}';

    protected $description = 'Run migrations for all tenants or single tenant';

    public function handle(TenantAppApiService $apiService)
    {
        $tenantId = $this->option('tenant');

        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::where('active', true)->get();

        foreach ($tenants as $tenant) {
            $data = $this->credentialsFor($tenant, $apiService);
            $this->info("Migrating tenant {$tenant->tenant_key} ({$data['db_name']})");

            if (($data['db_driver'] ?? null) === 'sqlite') {
                $response = $apiService->migrateTenantDatabase($this->mainFirmId($tenant));
                if (! ($response['success'] ?? false) || ! ($response['data']['success'] ?? false)) {
                    $this->error("Main app failed to migrate SQLite tenant {$tenant->tenant_key}: ".($response['error'] ?? 'unknown'));

                    continue;
                }

                $this->info("OK: {$tenant->tenant_key}");

                continue;
            }

            TenantManager::setConnectionFromArray($data);

            try {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--force' => true,
                ]);
                $this->info("OK: {$tenant->tenant_key}");
            } catch (\Exception $e) {
                $this->error("Error migrating {$tenant->tenant_key}: {$e->getMessage()}");
            }
        }

        return 0;
    }

    private function credentialsFor(Tenant $tenant, TenantAppApiService $apiService): array
    {
        $local = $tenant->only([
            'db_driver',
            'db_host',
            'db_port',
            'db_name',
            'db_user',
        ]);
        $local['db_password'] = $tenant->decrypted_password ?? $tenant->db_password ?? null;

        try {
            $response = $apiService->getTenantCredentials($this->mainFirmId($tenant));
            $credentials = $response['data']['data'] ?? null;

            if (($response['success'] ?? false)
                && ($response['data']['success'] ?? false)
                && is_array($credentials)) {
                return array_merge($local, $credentials);
            }
        } catch (\Throwable $e) {
            $response = ['error' => $e->getMessage()];
        }

        $error = $response['error'] ?? $response['data']['error'] ?? 'unknown';
        Log::warning('Using potentially stale local credentials for tenant '.$tenant->id.' - main app unreachable: '.$error);

        return $local;
    }

    private function mainFirmId(Tenant $tenant): int
    {
        return (int) ($tenant->main_firm_id ?: $tenant->id);
    }
}
