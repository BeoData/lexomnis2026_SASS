<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class TenantsBackup extends Command
{
    protected $signature = 'tenants:backup {--tenant=}';
    protected $description = 'Create SQL dump backups for all tenants and store in storage/app/backups';

    public function handle()
    {
        $tenantId = $this->option('tenant');

        $tenants = $tenantId ? Tenant::where('id', $tenantId)->get() : Tenant::where('active', true)->get();

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0750, true);
        }

        foreach ($tenants as $tenant) {
            $this->info("Backing up tenant {$tenant->tenant_key} ({$tenant->db_name})");

            $password = $tenant->decrypted_password ?? null;
            if (!$password) {
                try {
                    $password = Crypt::decryptString($tenant->db_password);
                } catch (\Throwable $e) {
                    $this->error("Could not decrypt password for tenant {$tenant->tenant_key}");
                    Log::error('Tenant backup decryption failed', ['tenant' => $tenant->tenant_key, 'error' => $e->getMessage()]);
                    continue;
                }
            }

            $host = $tenant->db_host ?? '127.0.0.1';
            $port = $tenant->db_port ?? '3306';
            $user = $tenant->db_user ?? 'root';
            $db = $tenant->db_name;

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
                if (!$process->isSuccessful()) {
                    $this->error("mysqldump failed for {$tenant->tenant_key}: " . $process->getErrorOutput());
                    Log::error('mysqldump failed', ['tenant' => $tenant->tenant_key, 'error' => $process->getErrorOutput()]);
                    @unlink($tmp);
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
                        $this->error("S3 upload failed for {$tenant->tenant_key}: " . $e->getMessage());
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
                $this->error("Error backing up {$tenant->tenant_key}: " . $e->getMessage());
                Log::error('Tenant backup error', ['tenant' => $tenant->tenant_key, 'error' => $e->getMessage()]);
            } finally {
                @unlink($tmp);
            }
        }

        return 0;
    }
}
