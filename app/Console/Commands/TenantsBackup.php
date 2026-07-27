<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class TenantsBackup extends Command
{
    protected $signature = 'tenants:backup {--tenant=}';

    protected $description = 'Create SQL dump backups for all tenants and store in storage/app/backups';

    public function handle(TenantAppApiService $apiService)
    {
        $failed = false;
        $tenantId = $this->option('tenant');

        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::where('active', true)->get();

        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir)) {
            mkdir($backupDir, 0750, true);
        }

        foreach ($tenants as $tenant) {
            $credentials = $this->credentialsFor($tenant, $apiService);
            $this->info("Backing up tenant {$tenant->tenant_key} ({$credentials['db_name']})");

            if (($credentials['db_driver'] ?? null) === 'sqlite') {
                $response = $apiService->backupTenantDatabase($this->mainFirmId($tenant));
                if (! ($response['success'] ?? false) || ! ($response['data']['success'] ?? false)) {
                    $this->error("Main app failed to back up SQLite tenant {$tenant->tenant_key}: ".($response['error'] ?? 'unknown'));
                    $failed = true;

                    continue;
                }

                $this->info("SQLite backup completed in main app: {$tenant->tenant_key}");

                continue;
            }

            $password = $credentials['db_password'];
            if (! $credentials['from_main'] && ! $password) {
                try {
                    $password = Crypt::decryptString($tenant->db_password);
                } catch (\Throwable $e) {
                    $this->error("Could not decrypt password for tenant {$tenant->tenant_key}");
                    Log::error('Tenant backup decryption failed', ['tenant' => $tenant->tenant_key, 'error' => $e->getMessage()]);
                    $failed = true;

                    continue;
                }
            }

            $host = $credentials['db_host'] ?? '127.0.0.1';
            $port = $credentials['db_port'] ?? '3306';
            $user = $credentials['db_user'] ?? 'root';
            $db = $credentials['db_name'];

            $timestamp = date('Ymd_His');
            $fileName = "{$tenant->tenant_key}_{$timestamp}.sql.gz";
            $filePath = "$backupDir/$fileName";

            // Use a temporary defaults-extra-file to avoid exposing password in process list
            $tmp = tempnam(sys_get_temp_dir(), 'mycnf');
            $cnf = "[client]\nuser={$user}\npassword='{$password}'\nhost={$host}\nport={$port}\n";
            file_put_contents($tmp, $cnf);

            $cmd = "mysqldump --defaults-extra-file=\"{$tmp}\" {$db}";

            $process = Process::fromShellCommandline($cmd);
            $process->setTimeout(300);

            try {
                $process->run();
                if (! $process->isSuccessful()) {
                    $this->error("mysqldump failed for {$tenant->tenant_key}: ".$process->getErrorOutput());
                    Log::error('mysqldump failed', ['tenant' => $tenant->tenant_key, 'error' => $process->getErrorOutput()]);
                    @unlink($tmp);
                    $failed = true;

                    continue;
                }

                $output = $process->getOutput();
                $gz = gzencode($output, 9);

                // Save locally
                file_put_contents($filePath, $gz);

                // Optionally upload to S3 if configured
                if (config('filesystems.disks.s3') && env('BACKUP_S3_UPLOAD', false)) {
                    try {
                        Storage::disk('s3')->put("backups/{$fileName}", $gz);
                        $this->info("Backup uploaded to S3: backups/{$fileName}");
                    } catch (\Throwable $e) {
                        $this->error("S3 upload failed for {$tenant->tenant_key}: ".$e->getMessage());
                        Log::error('S3 upload failed', ['tenant' => $tenant->tenant_key, 'error' => $e->getMessage()]);
                    }
                }

                $this->info("Backup saved: $filePath");
                Log::info('Tenant backup saved', ['tenant' => $tenant->tenant_key, 'file' => $filePath]);

                // Optional Slack notification
                $webhook = env('BACKUP_SLACK_WEBHOOK');
                if ($webhook) {
                    try {
                        Http::post($webhook, ['text' => "Backup created for {$tenant->tenant_key}: {$fileName}"]);
                    } catch (\Throwable $e) {
                        Log::warning('Slack notify failed', ['error' => $e->getMessage()]);
                    }
                }

            } catch (\Throwable $e) {
                $this->error("Error backing up {$tenant->tenant_key}: ".$e->getMessage());
                Log::error('Tenant backup error', ['tenant' => $tenant->tenant_key, 'error' => $e->getMessage()]);
                $failed = true;
            } finally {
                @unlink($tmp);
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function credentialsFor(Tenant $tenant, TenantAppApiService $apiService): array
    {
        $local = [
            'db_driver' => $tenant->db_driver,
            'db_host' => $tenant->db_host,
            'db_port' => $tenant->db_port,
            'db_name' => $tenant->db_name,
            'db_user' => $tenant->db_user,
            'db_password' => $tenant->decrypted_password,
            'from_main' => false,
        ];

        try {
            $response = $apiService->getTenantCredentials($this->mainFirmId($tenant));
            $credentials = $response['data']['data'] ?? null;

            if (($response['success'] ?? false)
                && ($response['data']['success'] ?? false)
                && is_array($credentials)) {
                return array_merge($local, $credentials, ['from_main' => true]);
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
