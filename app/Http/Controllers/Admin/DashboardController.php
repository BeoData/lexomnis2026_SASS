<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        // Safety check: Only SuperAdmin should be on port 8000 Dashboard
        // You can change 'admin@lexomnis.com' to your actual admin email
        $user = auth()->user();
        if ($user->email !== 'admin@lexomnis.com' && !str_ends_with($user->email, '@lexomnis.rs')) {
            return redirect('http://127.0.0.1:8001/login');
        }

        $data = $this->apiService->getDashboardData();

        return Inertia::render('Dashboard', [
            'stats' => [
                'tenants' => $data['tenants'] ?? [],
                'users' => $data['users'] ?? [],
                'subscriptions' => $data['subscriptions'] ?? [],
                'plans' => $data['plans'] ?? [],
                'system' => $data['system'] ?? [],
            ],
            'recentActivity' => $data['recentActivity'] ?? [],
        ]);
    }
}
