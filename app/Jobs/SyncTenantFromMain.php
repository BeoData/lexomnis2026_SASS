<?php

namespace App\Jobs;

use App\Services\TenantRegistrySyncService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncTenantFromMain implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public readonly int $mainFirmId) {}

    public function backoff(): array
    {
        return [60, 300, 900, 3600];
    }

    public function handle(TenantRegistrySyncService $sync): void
    {
        $sync->syncByMainId($this->mainFirmId);
    }
}
