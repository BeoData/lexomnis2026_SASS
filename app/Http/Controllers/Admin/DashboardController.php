<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;

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

        return view('admin.dashboard.index', [
            'stats' => [
                'tenants' => $data['tenants'],
                'users' => $data['users'],
                'subscriptions' => $data['subscriptions'],
                'plans' => $data['plans'],
                'system' => $data['system'],
            ],
            'recentActivity' => $data['recentActivity'],
        ]);
    }
}
