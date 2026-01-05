<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search']);
        $response = $this->apiService->getPlans($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch plans']);
        }

        return view('admin.plans.index', [
            'plans' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Map billing_cycle to billing_period for API
        $apiData = $validated;
        if (isset($apiData['billing_cycle'])) {
            $apiData['billing_period'] = $apiData['billing_cycle'];
            unset($apiData['billing_cycle']);
        }

        $response = $this->apiService->createPlan($apiData);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to create plan']);
        }

        return redirect()->route('plans.index')
            ->with('success', 'Plan created successfully');
    }

    public function show(string $id)
    {
        $response = $this->apiService->getPlan((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Plan not found']);
        }

        return view('admin.plans.show', [
            'plan' => $response['data'] ?? [],
        ]);
    }

    public function edit(string $id)
    {
        $response = $this->apiService->getPlan((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Plan not found']);
        }

        // Map billing_period from API to billing_cycle for form
        $plan = $response['data'] ?? [];
        if (isset($plan['billing_period'])) {
            $plan['billing_cycle'] = $plan['billing_period'];
        }

        return view('admin.plans.edit', [
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'billing_cycle' => 'sometimes|in:monthly,yearly',
            'features' => 'nullable|array',
            'limits' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Map billing_cycle to billing_period for API
        $apiData = $validated;
        if (isset($apiData['billing_cycle'])) {
            $apiData['billing_period'] = $apiData['billing_cycle'];
            unset($apiData['billing_cycle']);
        }

        $response = $this->apiService->updatePlan((int) $id, $apiData);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to update plan']);
        }

        return redirect()->route('plans.show', $id)
            ->with('success', 'Plan updated successfully');
    }
}
