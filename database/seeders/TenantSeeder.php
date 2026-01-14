<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use Illuminate\Support\Facades\Crypt;

class TenantSeeder extends Seeder
{
    public function run()
    {
        $dbName = env('TENANT_DB_NAME');
        if ($dbName) {
            Tenant::create([
                'tenant_key' => env('TENANT_KEY', 'tenant1'),
                'db_driver' => env('TENANT_DB_DRIVER', 'mysql'),
                'db_host' => env('TENANT_DB_HOST', '127.0.0.1'),
                'db_port' => env('TENANT_DB_PORT', '3306'),
                'db_name' => $dbName,
                'db_user' => env('TENANT_DB_USER', 'root'),
                'db_password' => Crypt::encryptString(env('TENANT_DB_PASSWORD', '')),
                'active' => true,
            ]);
        }
    }
}
