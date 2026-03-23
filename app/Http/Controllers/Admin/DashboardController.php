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
