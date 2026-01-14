<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantsDelete extends Command
{
    protected $signature = 'tenants:delete {tenant_key} {--mode=soft} {--backup} {--drop-db} {--force}';
    protected $description = 'Delete tenant safely: soft (default) or hard. Use --backup to create backup before delete.';

    public function handle()
    {
        $key = $this->argument('tenant_key');
        $mode = $this->option('mode');
        $doBackup = $this->option('backup');
        $dropDb = $this->option('drop-db');
        $force = $this->option('force');

        $tenant = Tenant::where('tenant_key', $key)->first();
        if (!$tenant) {
            $this->error("Tenant not found: {$key}");
            return 1;
        }

        if ($doBackup) {
            $this->info('Creating backup for tenant...');
            Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
            $this->info('Backup finished (check logs).');
        }

        if ($mode === 'soft') {
            $tenant->active = false;
            $tenant->save();
            try {
                $tenant->delete();
            } catch (\Throwable $e) {
                Log::warning('Soft delete failed', ['tenant' => $key, 'error' => $e->getMessage()]);
            }

            $this->info("Tenant {$key} soft-deleted (inactive).");
            return 0;
        }

        if ($mode === 'hard') {
            if (!$force) {
                $this->error('Hard delete requires --force to proceed.');
                return 1;
            }

            if (!$doBackup) {
                $this->warn('No backup requested; creating one automatically.');
                Artisan::call('tenants:backup', ['--tenant' => $tenant->id]);
            }

            try {
                $data = $tenant->toArray();
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
                $adminHost = env('DB_ADMIN_HOST', $tenant->db_host ?? '127.0.0.1');
                $adminPort = env('DB_ADMIN_PORT', $tenant->db_port ?? '3306');

                if (!$adminUser || !$adminPass) {
                    $this->error('DB admin credentials not configured in env (DB_ADMIN_USER / DB_ADMIN_PASSWORD). Skipping drop.');
                    Log::warning('Drop DB skipped due to missing admin credentials', ['tenant' => $key]);
                } else {
                    try {
                        $pdo = new \PDO("mysql:host={$adminHost};port={$adminPort}", $adminUser, $adminPass);
                        $pdo->exec("DROP DATABASE IF EXISTS `{$tenant->db_name}`");
                        $this->info("Database {$tenant->db_name} dropped.");
                    } catch (\Throwable $e) {
                        $this->error('Failed to drop database: ' . $e->getMessage());
                        Log::error('Drop database failed', ['tenant' => $key, 'error' => $e->getMessage()]);
                    }
                }
            }

            return 0;
        }

        $this->error('Unknown mode. Use --mode=soft or --mode=hard');
        return 1;
    }
}
