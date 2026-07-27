<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantsDelete extends Command
{
    protected $signature = 'tenants:delete {tenant_key} {--mode=soft} {--backup} {--drop-db} {--force}';

    protected $description = 'Delete tenant safely: soft (default) or hard. Use --backup to create backup before delete.';

    public function handle(TenantAppApiService $apiService)
    {
        $key = $this->argument('tenant_key');
        $mode = $this->option('mode');
        $doBackup = $this->option('backup');
        $dropDb = $this->option('drop-db');
        $force = $this->option('force');

        $tenant = Tenant::where('tenant_key', $key)->first();
        if (! $tenant) {
            $this->error("Tenant not found: {$key}");

            return self::FAILURE;
        }

        if ($mode === 'soft') {
            if ($doBackup) {
                $this->info('Creating backup for tenant...');
                Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
                $this->info('Backup finished (check logs).');
            }

            $tenant->active = false;
            $tenant->save();
            try {
                $tenant->delete();
            } catch (\Throwable $e) {
                Log::warning('Soft delete failed', ['tenant' => $key, 'error' => $e->getMessage()]);
            }

            $this->info("Tenant {$key} soft-deleted (inactive).");

            return self::SUCCESS;
        }

        if ($mode === 'hard') {
            if (! $force) {
                $this->error('Hard delete requires --force to proceed.');

                return self::FAILURE;
            }

            $dropCredentials = null;
            if ($dropDb) {
                $dropCredentials = $this->verifiedDropCredentials($tenant, $apiService);
                if ($dropCredentials === null) {
                    return self::FAILURE;
                }

                if (! env('DB_ADMIN_USER') || ! env('DB_ADMIN_PASSWORD')) {
                    $this->error('DB admin credentials not configured in env (DB_ADMIN_USER / DB_ADMIN_PASSWORD). Aborting hard delete.');
                    Log::warning('Hard delete aborted due to missing DB admin credentials', ['tenant' => $key]);

                    return self::FAILURE;
                }
            }

            if ($doBackup) {
                $this->info('Creating backup for tenant...');
                Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
                $this->info('Backup finished (check logs).');
            } else {
                $this->warn('No backup requested; creating one automatically.');
                Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
            }

            try {
                $data = $tenant->toArray();
                if ($dropCredentials !== null) {
                    foreach (['db_driver', 'db_host', 'db_port', 'db_name', 'db_user'] as $field) {
                        $data[$field] = $dropCredentials[$field];
                    }
                }
                $data['archived_at'] = now();
                DB::table('tenants_archive')->insert($data);
            } catch (\Throwable $e) {
                Log::error('Archiving tenant failed', ['tenant' => $key, 'error' => $e->getMessage()]);
            }

            try {
                $tenant->forceDelete();
            } catch (\Throwable $e) {
                Log::error('Deleting tenant record failed', ['tenant' => $key, 'error' => $e->getMessage()]);
            }

            $this->info("Tenant {$key} removed from central table and archived.");

            if ($dropDb) {
                $this->info('Attempting to drop tenant database...');

                $adminUser = env('DB_ADMIN_USER');
                $adminPass = env('DB_ADMIN_PASSWORD');
                $adminHost = env('DB_ADMIN_HOST', $dropCredentials['db_host'] ?? '127.0.0.1');
                $adminPort = env('DB_ADMIN_PORT', $dropCredentials['db_port'] ?? '3306');
                $dbName = $dropCredentials['db_name'];

                try {
                    $pdo = new \PDO("mysql:host={$adminHost};port={$adminPort}", $adminUser, $adminPass);
                    $quotedDbName = str_replace('`', '``', $dbName);
                    $pdo->exec("DROP DATABASE IF EXISTS `{$quotedDbName}`");
                    $this->info("Database {$dbName} dropped.");
                } catch (\Throwable $e) {
                    $this->error('Failed to drop database: '.$e->getMessage());
                    Log::error('Drop database failed', ['tenant' => $key, 'error' => $e->getMessage()]);
                }
            }

            return self::SUCCESS;
        }

        $this->error('Unknown mode. Use --mode=soft or --mode=hard');

        return self::FAILURE;
    }

    private function verifiedDropCredentials(Tenant $tenant, TenantAppApiService $apiService): ?array
    {
        try {
            $response = $apiService->getTenantCredentials((int) $tenant->id);
            $credentials = $response['data']['data'] ?? null;

            if (($response['success'] ?? false)
                && ($response['data']['success'] ?? false)
                && is_array($credentials)
                && ($credentials['db_driver'] ?? null) === 'mysql'
                && ! empty($credentials['db_name'])) {
                return $credentials;
            }
        } catch (\Throwable $e) {
            $response = ['error' => $e->getMessage()];
        }

        $error = $response['error'] ?? $response['data']['error'] ?? 'invalid or non-MySQL database credentials';
        $this->error('Unable to verify current tenant database target from main app. Hard delete aborted.');
        Log::warning('Tenant database drop aborted because current main credentials could not be verified', [
            'tenant' => $tenant->tenant_key,
            'firm_id' => $tenant->id,
            'error' => $error,
        ]);

        return null;
    }
}
