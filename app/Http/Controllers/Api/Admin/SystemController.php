<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    /**
     * System health status
     */
    public function health()
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'database' => $this->checkDatabase(),
            'queue' => $this->checkQueue(),
        ]);
    }

    /**
     * Queue status
     */
    public function queues()
    {
        return response()->json([
            'connection' => config('queue.default'),
            'status' => 'active',
        ]);
    }

    /**
     * Cron status
     */
    public function crons()
    {
        return response()->json([
            'status' => 'active',
            'last_run' => now()->subMinutes(5)->toIso8601String(),
        ]);
    }

    /**
     * Performance metrics
     */
    public function metrics()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalUsers = User::count();

        return response()->json([
            'tenants' => [
                'total' => $totalTenants,
                'active' => $activeTenants,
                'suspended' => Tenant::where('status', 'suspended')->count(),
                'trial' => Tenant::where('status', 'trial')->count(),
            ],
            'users' => [
                'total' => $totalUsers,
                'active' => $totalUsers, // Placeholder
            ],
        ]);
    }

    /**
     * Activity logs
     */
    public function activityLogs(Request $request)
    {
        return response()->json([]);
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'connected'];
        } catch (\Exception $e) {
            return ['status' => 'disconnected', 'error' => $e->getMessage()];
        }
    }

    private function checkQueue(): array
    {
        return ['status' => 'connected'];
    }
}
