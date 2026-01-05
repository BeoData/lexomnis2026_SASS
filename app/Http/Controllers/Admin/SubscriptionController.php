<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['tenant_id', 'plan_id', 'status', 'search']);
        $response = $this->apiService->getSubscriptions($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch subscriptions']);
        }

        return view('admin.subscriptions.index', [
            'subscriptions' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function show(string $id)
    {
        $response = $this->apiService->getSubscription((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Subscription not found']);
        }

        return view('admin.subscriptions.show', [
            'subscription' => $response['data'] ?? [],
        ]);
    }
}
