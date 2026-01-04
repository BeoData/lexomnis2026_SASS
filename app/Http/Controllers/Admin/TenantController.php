<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TenantController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'search', 'plan_id']);
        $response = $this->apiService->getTenants($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch tenants']);
        }

        return Inertia::render('Tenants/Index', [
            'tenants' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenants/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255|default:RS',
            'timezone' => 'nullable|string|default:Europe/Belgrade',
            'currency' => 'nullable|string|max:3|default:RSD',
        ]);

        $response = $this->apiService->createTenant($validated);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to create tenant']);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant created successfully');
    }

    public function show(string $id)
    {
        $response = $this->apiService->getTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Tenant not found']);
        }

        return Inertia::render('Tenants/Show', [
            'tenant' => $response['data'] ?? [],
        ]);
    }

    public function edit(string $id)
    {
        $response = $this->apiService->getTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Tenant not found']);
        }

        return Inertia::render('Tenants/Edit', [
            'tenant' => $response['data'] ?? [],
        ]);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'status' => 'sometimes|in:active,suspended,trial,deleted',
            'country' => 'nullable|string|max:255',
            'timezone' => 'nullable|string',
            'currency' => 'nullable|string|max:3',
        ]);

        $response = $this->apiService->updateTenant((int) $id, $validated);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to update tenant']);
        }

        return redirect()->route('tenants.show', $id)
            ->with('success', 'Tenant updated successfully');
    }

    public function suspend(string $id)
    {
        $response = $this->apiService->suspendTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to suspend tenant']);
        }

        return back()->with('success', 'Tenant suspended successfully');
    }

    public function activate(string $id)
    {
        $response = $this->apiService->activateTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to activate tenant']);
        }

        return back()->with('success', 'Tenant activated successfully');
    }
}
