<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class FeatureFlagController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['tenant_id', 'is_active', 'search']);
        $response = $this->apiService->getFeatureFlags($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch feature flags']);
        }

        // API returns direct collection (not paginated)
        $featureFlags = is_array($response['data']) && isset($response['data']['data']) 
            ? $response['data']['data'] 
            : (is_array($response['data']) ? $response['data'] : []);

        return view('admin.feature-flags.index', [
            'featureFlags' => $featureFlags,
            'pagination' => [],
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return view('admin.feature-flags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'tenant_id' => 'nullable|integer',
            'environment' => 'required|in:all,production,staging,development',
        ]);

        // Map fields to API format
        $apiData = [
            'name' => $validated['name'],
            'key' => $validated['key'],
            'description' => $validated['description'] ?? null,
            'enabled' => $validated['is_active'] ?? false,
            'firm_id' => $validated['tenant_id'] ?? null,
            'is_global' => empty($validated['tenant_id']),
            'environment' => $validated['environment'] ?? 'all',
        ];

        $response = $this->apiService->createFeatureFlag($apiData);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to create feature flag'])->withInput();
        }

        return redirect()->route('feature-flags.index')
            ->with('success', 'Feature flag created successfully');
    }

    public function show(string $id)
    {
        $response = $this->apiService->getFeatureFlag((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Feature flag not found']);
        }

        $featureFlag = $response['data'] ?? null;

        if (!$featureFlag) {
            return back()->withErrors(['error' => 'Feature flag not found']);
        }

        return view('admin.feature-flags.show', [
            'featureFlag' => $featureFlag,
        ]);
    }

    public function edit(string $id)
    {
        $response = $this->apiService->getFeatureFlag((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Feature flag not found']);
        }

        $featureFlag = $response['data'] ?? null;

        if (!$featureFlag) {
            return back()->withErrors(['error' => 'Feature flag not found']);
        }

        return view('admin.feature-flags.edit', [
            'featureFlag' => $featureFlag,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'tenant_id' => 'nullable|integer',
            'environment' => 'sometimes|in:all,production,staging,development',
        ]);

        // Map fields to API format
        $apiData = [];
        if (isset($validated['name'])) {
            $apiData['name'] = $validated['name'];
        }
        if (isset($validated['key'])) {
            $apiData['key'] = $validated['key'];
        }
        if (isset($validated['description'])) {
            $apiData['description'] = $validated['description'];
        }
        if (isset($validated['is_active'])) {
            $apiData['enabled'] = $validated['is_active'];
        }
        if (isset($validated['tenant_id'])) {
            $apiData['firm_id'] = $validated['tenant_id'];
            $apiData['is_global'] = empty($validated['tenant_id']);
        }
        if (isset($validated['environment'])) {
            $apiData['environment'] = $validated['environment'];
        }

        $response = $this->apiService->updateFeatureFlag((int) $id, $apiData);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to update feature flag'])->withInput();
        }

        return redirect()->route('feature-flags.show', $id)
            ->with('success', 'Feature flag updated successfully');
    }

    public function destroy(string $id)
    {
        // Note: API might not support DELETE, check TenantAppApiService
        return back()->withErrors(['error' => 'Delete not implemented yet']);
    }
}
