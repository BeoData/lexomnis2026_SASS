<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\TenantAppApiService;
use Illuminate\Console\Command;

class TenantsVerifySync extends Command
{
    protected $signature = 'tenants:verify-sync';

    protected $description = 'Compare local tenant database credentials with the main app without changing data';

    public function handle(TenantAppApiService $apiService): int
    {
        $rows = [];

        Tenant::query()->orderBy('id')->each(function (Tenant $tenant) use ($apiService, &$rows): void {
            $response = $apiService->getTenantCredentials((int) $tenant->id);
            $main = $response['data']['data'] ?? null;

            if (! ($response['success'] ?? false)
                || ! ($response['data']['success'] ?? false)
                || ! is_array($main)) {
                $rows[] = [
                    $tenant->tenant_key,
                    $tenant->id,
                    'connection',
                    'available',
                    'unavailable',
                    $response['error'] ?? 'unknown',
                ];

                return;
            }

            foreach (['db_driver', 'db_host', 'db_port', 'db_name', 'db_user'] as $field) {
                $localValue = (string) ($tenant->{$field} ?? '');
                $mainValue = (string) ($main[$field] ?? '');

                $rows[] = [
                    $tenant->tenant_key,
                    $tenant->id,
                    $field,
                    $localValue,
                    $mainValue,
                    hash_equals($localValue, $mainValue) ? 'MATCH' : 'MISMATCH',
                ];
            }

            $localPassword = (string) ($tenant->decrypted_password ?? '');
            $mainPassword = (string) ($main['db_password'] ?? '');
            $passwordStatus = hash_equals($localPassword, $mainPassword) ? 'MATCH' : 'MISMATCH';

            $rows[] = [
                $tenant->tenant_key,
                $tenant->id,
                'db_password',
                $passwordStatus,
                $passwordStatus,
                $passwordStatus,
            ];
        });

        $this->table(
            ['Tenant', 'Main firm ID', 'Field', 'Local', 'Main', 'Status'],
            $rows
        );

        return self::SUCCESS;
    }
}
