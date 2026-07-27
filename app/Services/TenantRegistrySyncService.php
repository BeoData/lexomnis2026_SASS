<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TenantRegistrySyncService
{
    public function __construct(private readonly TenantAppApiService $apiService) {}

    public function syncByMainId(int $mainFirmId): Tenant
    {
        $firmResponse = $this->apiService->getTenant($mainFirmId);
        if (! ($firmResponse['success'] ?? false) || ! is_array($firmResponse['data'] ?? null)) {
            throw new RuntimeException($firmResponse['error'] ?? "Unable to load main firm {$mainFirmId}.");
        }

        return $this->syncPayload($firmResponse['data']);
    }

    public function syncPayload(array $firm): Tenant
    {
        $mainFirmId = (int) ($firm['id'] ?? 0);
        if ($mainFirmId < 1 || empty($firm['slug'])) {
            throw new RuntimeException('Main firm payload is missing id or slug.');
        }

        $credentialsResponse = $this->apiService->getTenantCredentials($mainFirmId);
        $credentials = $credentialsResponse['data']['data'] ?? null;
        if (! ($credentialsResponse['success'] ?? false)
            || ! ($credentialsResponse['data']['success'] ?? false)
            || ! is_array($credentials)) {
            throw new RuntimeException($credentialsResponse['error'] ?? "Unable to load credentials for main firm {$mainFirmId}.");
        }

        return DB::transaction(function () use ($firm, $credentials, $mainFirmId): Tenant {
            $tenant = Tenant::withTrashed()->where('main_firm_id', $mainFirmId)->first();
            if (! $tenant) {
                $tenant = Tenant::withTrashed()->whereKey($mainFirmId)->first() ?? new Tenant;
                if (! $tenant->exists) {
                    $tenant->id = $mainFirmId;
                }
            }

            $tenant->fill([
                'main_firm_id' => $mainFirmId,
                'tenant_key' => $firm['slug'],
                'db_driver' => $credentials['db_driver'] ?? 'sqlite',
                'db_host' => $credentials['db_host'] ?? '',
                'db_port' => (string) ($credentials['db_port'] ?? ''),
                'db_name' => $credentials['db_name'] ?? '',
                'db_user' => $credentials['db_user'] ?? '',
                'db_password' => Crypt::encryptString((string) ($credentials['db_password'] ?? '')),
                'active' => ($firm['status'] ?? null) === 'active',
                'sync_status' => 'synced',
                'sync_error' => null,
                'last_synced_at' => now(),
                'meta' => array_merge($tenant->meta ?? [], [
                    'name' => $firm['name'] ?? null,
                    'main_status' => $firm['status'] ?? null,
                ]),
            ]);
            $tenant->save();

            if ($tenant->trashed()) {
                $tenant->restore();
            }

            return $tenant->fresh();
        });
    }

    public function syncAll(): array
    {
        $page = 1;
        $seen = [];
        $synced = 0;

        do {
            $response = $this->apiService->getTenants(['page' => $page, 'per_page' => 100]);
            $payload = $response['data'] ?? null;
            if (! ($response['success'] ?? false) || ! is_array($payload)) {
                throw new RuntimeException($response['error'] ?? 'Unable to load main tenant registry.');
            }

            foreach ($payload['data'] ?? [] as $firm) {
                $tenant = $this->syncPayload($firm);
                $seen[] = $tenant->main_firm_id;
                $synced++;
            }

            $lastPage = (int) ($payload['last_page'] ?? 1);
            $page++;
        } while ($page <= $lastPage);

        Tenant::query()->whereNotIn('main_firm_id', $seen)->update([
            'active' => false,
            'sync_status' => 'missing',
            'sync_error' => 'Firm is not present in the authoritative main registry.',
        ]);

        return ['synced' => $synced, 'missing' => Tenant::where('sync_status', 'missing')->count()];
    }

    public function removeLocal(int $mainFirmId): void
    {
        Tenant::where('main_firm_id', $mainFirmId)->delete();
    }
}
