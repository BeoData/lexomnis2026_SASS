<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;

class TenantsClearCache extends Command
{
    protected $signature = 'tenants:clear-cache {tenant?}';
    protected $description = 'Clear tenant cache for a specific tenant (id or key) or for all tenants if no argument provided.';

    public function handle()
    {
        $arg = $this->argument('tenant');

        if ($arg) {
            if (is_numeric($arg)) {
                $cacheKey = "tenant_id:{$arg}";
                Cache::forget($cacheKey);
                $t = Tenant::find($arg);
                if ($t) {
                    Cache::forget("tenant_key:{$t->tenant_key}");
                }
                $this->info("Cleared tenant cache for id={$arg}");
                return 0;
            }

            $cacheKey = "tenant_key:{$arg}";
            Cache::forget($cacheKey);
            $t = Tenant::where('tenant_key', $arg)->first();
            if ($t) {
                Cache::forget("tenant_id:{$t->id}");
            }
            $this->info("Cleared tenant cache for key={$arg}");
            return 0;
        }

        $this->info('Clearing cache for all tenants...');
        Tenant::chunk(100, function ($tenants) {
            foreach ($tenants as $t) {
                Cache::forget("tenant_id:{$t->id}");
                if ($t->tenant_key) {
                    Cache::forget("tenant_key:{$t->tenant_key}");
                }
            }
        });

        $this->info('All tenant cache entries cleared.');
        return 0;
    }
}
