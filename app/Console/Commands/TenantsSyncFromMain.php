<?php

namespace App\Console\Commands;

use App\Services\TenantRegistrySyncService;
use Illuminate\Console\Command;

class TenantsSyncFromMain extends Command
{
    protected $signature = 'tenants:sync-from-main {--execute : Apply changes to the local tenant registry}';

    protected $description = 'Synchronize the local SASS tenant registry from the authoritative main API';

    public function handle(TenantRegistrySyncService $sync): int
    {
        if (! $this->option('execute')) {
            $this->info('Dry run only. Re-run with --execute to update the local tenant registry.');

            return self::SUCCESS;
        }

        $result = $sync->syncAll();
        $this->info("Synchronized {$result['synced']} tenants; {$result['missing']} local records marked missing.");

        return self::SUCCESS;
    }
}
