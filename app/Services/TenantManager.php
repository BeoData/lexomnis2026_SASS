<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    public static function setConnectionFromArray(array $tenant)
    {
        $name = 'tenant';

        $config = [
            'driver' => $tenant['db_driver'] ?? 'mysql',
            'host' => $tenant['db_host'] ?? '127.0.0.1',
            'port' => $tenant['db_port'] ?? 3306,
            'database' => $tenant['db_name'] ?? null,
            'username' => $tenant['db_user'] ?? null,
            'password' => $tenant['db_password'] ?? null,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ];

        Config::set("database.connections.{$name}", $config);

        DB::purge($name);
        DB::reconnect($name);
    }
}
