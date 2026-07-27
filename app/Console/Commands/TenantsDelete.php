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

            $response = $apiService->suspendTenant($this->mainFirmId($tenant));
            if (! ($response['success'] ?? false)) {
                $this->error('Main app failed to suspend tenant: '.($response['error'] ?? 'unknown'));

                return self::FAILURE;
            }

            $tenant->update(['active' => false, 'sync_status' => 'synced', 'last_synced_at' => now()]);
            $this->info("Tenant {$key} suspended in main app and marked inactive locally.");

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

                if (($dropCredentials['db_driver'] ?? null) !== 'sqlite'
                    && (! env('DB_ADMIN_USER') || ! env('DB_ADMIN_PASSWORD'))) {
                    $this->error('DB admin credentials not configured in env (DB_ADMIN_USER / DB_ADMIN_PASSWORD). Aborting hard delete.');
                    Log::warning('Hard delete aborted due to missing DB admin credentials', [
                        'firm_id' => $this->mainFirmId($tenant),
                        'tenant' => $key,
                    ]);

                    return self::FAILURE;
                }
            }

            $this->info($doBackup ? 'Creating requested backup for tenant...' : 'Creating mandatory backup before hard delete...');
            $backupExitCode = Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
            if ($backupExitCode !== self::SUCCESS) {
                $this->error('Backup failed. Hard delete aborted.');
                Log::warning('Tenant hard delete aborted because backup failed', [
                    'firm_id' => $this->mainFirmId($tenant),
                    'tenant' => $key,
                ]);

                return self::FAILURE;
            }
            $this->info('Backup finished.');

            try {
                $data = $tenant->only([
                    'main_firm_id',
                    'tenant_key',
                    'db_driver',
                    'db_host',
                    'db_port',
                    'db_name',
                    'db_user',
                    'db_password',
                    'active',
                    'sync_status',
                    'sync_error',
                    'last_synced_at',
                    'meta',
                    'created_at',
                    'updated_at',
                ]);
                if ($dropCredentials !== null) {
                    foreach (['db_driver', 'db_host', 'db_port', 'db_name', 'db_user'] as $field) {
                        $data[$field] = $dropCredentials[$field];
                    }
                }
                $data['archived_at'] = now();
                DB::table('tenants_archive')->insert($data);
            } catch (\Throwable $e) {
                $this->error('Failed to archive tenant. Hard delete aborted.');
                Log::error('Archiving tenant failed', [
                    'firm_id' => $this->mainFirmId($tenant),
                    'tenant' => $key,
                    'error' => $e->getMessage(),
                ]);

                return self::FAILURE;
            }

            $deleteResponse = $apiService->deleteTenant($this->mainFirmId($tenant));
            if (! ($deleteResponse['success'] ?? false) && ($deleteResponse['status'] ?? null) !== 404) {
                $this->error('Main app failed to delete tenant: '.($deleteResponse['error'] ?? 'unknown'));

                return self::FAILURE;
            }

            if ($dropDb) {
                $this->info('Attempting to drop tenant database...');

                $dbName = $dropCredentials['db_name'];

                if (($dropCredentials['db_driver'] ?? null) === 'sqlite') {
                    $dropResponse = $apiService->deleteTenantDatabase($this->mainFirmId($tenant), $dbName);
                    if (! ($dropResponse['success'] ?? false) || ! ($dropResponse['data']['success'] ?? false)) {
                        $this->error('Main app failed to delete SQLite tenant database: '.($dropResponse['error'] ?? 'unknown'));

                        return self::FAILURE;
                    }
                } else {
                    $adminUser = env('DB_ADMIN_USER');
                    $adminPass = env('DB_ADMIN_PASSWORD');
                    $adminHost = env('DB_ADMIN_HOST', $dropCredentials['db_host'] ?? '127.0.0.1');
                    $adminPort = env('DB_ADMIN_PORT', $dropCredentials['db_port'] ?? '3306');

                    try {
                        $pdo = new \PDO("mysql:host={$adminHost};port={$adminPort}", $adminUser, $adminPass);
                        $quotedDbName = str_replace('`', '``', $dbName);
                        $pdo->exec("DROP DATABASE IF EXISTS `{$quotedDbName}`");
                    } catch (\Throwable $e) {
                        $this->error('Failed to drop database: '.$e->getMessage());
                        Log::error('Drop database failed', [
                            'firm_id' => $this->mainFirmId($tenant),
                            'tenant' => $key,
                            'error' => $e->getMessage(),
                        ]);

                        return self::FAILURE;
                    }
                }

                $this->info("Database {$dbName} dropped.");
            }

            try {
                $tenant->forceDelete();
            } catch (\Throwable $e) {
                Log::error('Deleting tenant record failed', [
                    'firm_id' => $this->mainFirmId($tenant),
                    'tenant' => $key,
                    'error' => $e->getMessage(),
                ]);

                return self::FAILURE;
            }

            $this->info("Tenant {$key} removed from main and local registries and archived.");
            Log::info('Tenant hard delete completed', [
                'firm_id' => $this->mainFirmId($tenant),
                'tenant' => $key,
                'database_dropped' => $dropDb,
            ]);

            return self::SUCCESS;
        }

        $this->error('Unknown mode. Use --mode=soft or --mode=hard');

        return self::FAILURE;
    }

    private function verifiedDropCredentials(Tenant $tenant, TenantAppApiService $apiService): ?array
    {
        try {
            $response = $apiService->getTenantCredentials($this->mainFirmId($tenant));
            $credentials = $response['data']['data'] ?? null;

            if (($response['success'] ?? false)
                && ($response['data']['success'] ?? false)
                && is_array($credentials)
                && in_array(($credentials['db_driver'] ?? null), ['sqlite', 'mysql', 'mariadb'], true)
                && ! empty($credentials['db_name'])) {
                return $credentials;
            }
        } catch (\Throwable $e) {
            $response = ['error' => $e->getMessage()];
        }

        $error = $response['error'] ?? $response['data']['error'] ?? 'invalid database credentials';
        $this->error('Unable to verify current tenant database target from main app. Hard delete aborted.');
        Log::warning('Tenant database drop aborted because current main credentials could not be verified', [
            'tenant' => $tenant->tenant_key,
            'firm_id' => $tenant->id,
            'error' => $error,
        ]);

        return null;
    }

    private function mainFirmId(Tenant $tenant): int
    {
        return (int) ($tenant->main_firm_id ?: $tenant->id);
    }
}
