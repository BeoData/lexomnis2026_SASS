<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;

class SystemController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index()
    {
        $health = $this->apiService->getSystemHealth();
        $metrics = $this->apiService->getMetrics();
        $queues = $this->apiService->getQueueStatus();
        $crons = $this->apiService->getCronStatus();

        return view('admin.system.index', [
            'health' => $health['success'] ? $health['data'] : null,
            'metrics' => $metrics['success'] ? $metrics['data'] : null,
            'queues' => $queues['success'] ? $queues['data'] : null,
            'crons' => $crons['success'] ? $crons['data'] : null,
        ]);
    }

    public function health()
    {
        $response = $this->apiService->getSystemHealth();

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch system health']);
        }

        return view('admin.system.health', [
            'health' => $response['data'] ?? [],
        ]);
    }

    public function metrics()
    {
        $response = $this->apiService->getMetrics();

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch metrics']);
        }

        return view('admin.system.metrics', [
            'metrics' => $response['data'] ?? [],
        ]);
    }
}
