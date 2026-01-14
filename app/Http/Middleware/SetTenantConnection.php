<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Tenant;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $tenantLookup = $user->tenant_id ?? $user->tenant_key ?? null;
        if (!$tenantLookup) {
            return $next($request);
        }

        $cacheKey = is_numeric($tenantLookup) ? "tenant_id:{$tenantLookup}" : "tenant_key:{$tenantLookup}";

        $useForever = config('tenancy.cache_forever', env('TENANT_CACHE_FOREVER', true));
        $ttl = config('tenancy.cache_ttl', env('TENANT_CACHE_TTL', 86400));

        if ($useForever) {
            $tenantConfig = Cache::rememberForever($cacheKey, function () use ($tenantLookup) {
                if (is_numeric($tenantLookup)) {
                    return Tenant::find($tenantLookup);
                }
                return Tenant::where('tenant_key', $tenantLookup)->first();
            });
        } else {
            $tenantConfig = Cache::remember($cacheKey, $ttl, function () use ($tenantLookup) {
                if (is_numeric($tenantLookup)) {
                    return Tenant::find($tenantLookup);
                }
                return Tenant::where('tenant_key', $tenantLookup)->first();
            });
        }

        if (!$tenantConfig) {
            abort(403, 'Tenant not found.');
        }

        $password = $tenantConfig->decrypted_password ?? null;
        if (!$password && $tenantConfig->db_password) {
            try {
                $password = Crypt::decryptString($tenantConfig->db_password);
            } catch (\Throwable $e) {
                $password = $tenantConfig->db_password;
            }
        }

        $connection = [
            'driver' => $tenantConfig->db_driver ?? 'mysql',
            'host' => $tenantConfig->db_host ?? env('DB_HOST', '127.0.0.1'),
            'port' => $tenantConfig->db_port ?? env('DB_PORT', '3306'),
            'database' => $tenantConfig->db_name,
            'username' => $tenantConfig->db_user,
            'password' => $password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ];

        Config::set('database.connections.tenant', $connection);
        DB::purge('tenant');
        DB::reconnect('tenant');
        DB::setDefaultConnection('tenant');

        return $next($request);
    }
}
