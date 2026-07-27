<?php

namespace App\Console;

use App\Console\Commands\TenantsBackup;
use App\Console\Commands\TenantsMigrate;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        TenantsMigrate::class,
        TenantsBackup::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
