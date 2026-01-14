<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;
use App\Services\TenantManager;

class TenantsMigrate extends Command
{
    protected $signature = 'tenants:migrate {--tenant=}';
    protected $description = 'Run migrations for all tenants or single tenant';

    public function handle()
    {
        $tenantId = $this->option('tenant');

        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::where('active', true)->get();

        foreach ($tenants as $tenant) {
            $this->info("Migrating tenant {$tenant->tenant_key} ({$tenant->db_name})");
            $data = $tenant->toArray();
            $data['db_password'] = $tenant->decrypted_password ?? $tenant->db_password ?? null;
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
}
