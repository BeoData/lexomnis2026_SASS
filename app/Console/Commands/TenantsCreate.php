<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Crypt;

class TenantsCreate extends Command
{
    protected $signature = 'tenants:create {tenant_key} {db_name} {db_user} {db_password} {--host=127.0.0.1} {--port=3306} {--driver=mysql}';
    protected $description = 'Create a tenant record in central DB (encrypts password)';

    public function handle()
    {
        $tenantKey = $this->argument('tenant_key');
        $dbName = $this->argument('db_name');
        $dbUser = $this->argument('db_user');
        $dbPassword = $this->argument('db_password');

        $tenant = Tenant::create([
            'tenant_key' => $tenantKey,
            'db_driver' => $this->option('driver'),
            'db_host' => $this->option('host'),
            'db_port' => $this->option('port'),
            'db_name' => $dbName,
            'db_user' => $dbUser,
            'db_password' => Crypt::encryptString($dbPassword),
            'active' => true,
        ]);

        $this->info("Tenant created: {$tenant->tenant_key}");
        return 0;
    }
}
