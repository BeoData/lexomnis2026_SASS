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

        $response = $this->apiService->createPlan($validated);

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

        return view('admin.plans.edit', [
            'plan' => $response['data'] ?? [],
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

        $response = $this->apiService->updatePlan((int) $id, $validated);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to update plan']);
        }

        return redirect()->route('plans.show', $id)
            ->with('success', 'Plan updated successfully');
    }
}
