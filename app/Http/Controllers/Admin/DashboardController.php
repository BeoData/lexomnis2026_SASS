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
        // Fetch statistics from API
        $stats = [
            'tenants' => $this->getTenantStats(),
            'users' => $this->getUserStats(),
            'subscriptions' => $this->getSubscriptionStats(),
            'plans' => $this->getPlanStats(),
            'system' => $this->getSystemStats(),
        ];

        // Fetch recent activity
        $recentActivity = $this->getRecentActivity();

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    protected function getTenantStats(): array
    {
        $response = $this->apiService->getTenants(['per_page' => 1]);
        
        if (!$response['success']) {
            return [
                'total' => 0,
                'active' => 0,
                'suspended' => 0,
                'trial' => 0,
            ];
        }

        $pagination = $response['data'] ?? [];
        $total = $pagination['total'] ?? 0;

        // Get counts by status
        $activeResponse = $this->apiService->getTenants(['status' => 'active', 'per_page' => 1]);
        $suspendedResponse = $this->apiService->getTenants(['status' => 'suspended', 'per_page' => 1]);
        $trialResponse = $this->apiService->getTenants(['status' => 'trial', 'per_page' => 1]);

        return [
            'total' => $total,
            'active' => ($activeResponse['success'] ?? false) ? ($activeResponse['data']['total'] ?? 0) : 0,
            'suspended' => ($suspendedResponse['success'] ?? false) ? ($suspendedResponse['data']['total'] ?? 0) : 0,
            'trial' => ($trialResponse['success'] ?? false) ? ($trialResponse['data']['total'] ?? 0) : 0,
        ];
    }

    protected function getUserStats(): array
    {
        $response = $this->apiService->getUsers(['per_page' => 1]);
        
        if (!$response['success']) {
            return [
                'total' => 0,
                'active' => 0,
                'suspended' => 0,
            ];
        }

        $pagination = $response['data'] ?? [];
        $total = $pagination['total'] ?? 0;

        return [
            'total' => $total,
            'active' => $total, // Assuming all are active if not specified
            'suspended' => 0,
        ];
    }

    protected function getSubscriptionStats(): array
    {
        $response = $this->apiService->getSubscriptions(['per_page' => 1]);
        
        if (!$response['success']) {
            return [
                'total' => 0,
                'active' => 0,
                'expired' => 0,
            ];
        }

        $pagination = $response['data'] ?? [];
        $total = $pagination['total'] ?? 0;

        $activeResponse = $this->apiService->getSubscriptions(['status' => 'active', 'per_page' => 1]);

        return [
            'total' => $total,
            'active' => ($activeResponse['success'] ?? false) ? ($activeResponse['data']['total'] ?? 0) : 0,
            'expired' => 0,
        ];
    }

    protected function getPlanStats(): array
    {
        $response = $this->apiService->getPlans(['per_page' => 1]);
        
        if (!$response['success']) {
            return [
                'total' => 0,
                'active' => 0,
            ];
        }

        $pagination = $response['data'] ?? [];
        $total = $pagination['total'] ?? 0;

        return [
            'total' => $total,
            'active' => $total,
        ];
    }

    protected function getSystemStats(): array
    {
        $healthResponse = $this->apiService->getSystemHealth();
        
        return [
            'status' => ($healthResponse['success'] ?? false) ? 'healthy' : 'unhealthy',
            'api_connected' => $healthResponse['success'] ?? false,
        ];
    }

    protected function getRecentActivity(): array
    {
        $response = $this->apiService->getAuditLogs(['per_page' => 10]);
        
        if (!$response['success']) {
            return [];
        }

        return $response['data']['data'] ?? [];
    }
}
