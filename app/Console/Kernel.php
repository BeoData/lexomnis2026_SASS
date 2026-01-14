<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\TenantsMigrate::class,
        \App\Console\Commands\TenantsBackup::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('tenants:backup')->dailyAt('02:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
