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

        return view('admin.tenants.index', [
            'tenants' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        // Load plans from API to pass to the view
        // Note: getPlans accepts filters array, but we need to pass grouped as query parameter
        // We'll need to modify the request to include grouped parameter
        $plansResponse = $this->apiService->getPlans(['is_active' => true]);
        
        $plans = $plansResponse['success'] ? ($plansResponse['data'] ?? []) : [];
        
        // If data is paginated, extract the data array
        if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
            $plans = $plans['data'];
        }
        
        // Group plans by plan_key manually
        $groupedPlans = [];
        if (is_array($plans) && !empty($plans)) {
            $grouped = collect($plans)->groupBy('plan_key')->map(function ($group, $planKey) {
                $first = $group->first();
                return [
                    'plan_key' => $planKey,
                    'name' => $first['name'] ?? '',
                    'monthly' => $group->firstWhere('billing_period', 'monthly'),
                    'yearly' => $group->firstWhere('billing_period', 'yearly'),
                    'metadata' => $first['metadata'] ?? [],
                ];
            })->values()->toArray();
            
            $groupedPlans = $grouped;
        }

        \Log::info('Tenant create - grouped plans', ['count' => count($groupedPlans), 'plans' => $groupedPlans]);

        return Inertia::render('Tenants/Create', [
            'groupedPlans' => $groupedPlans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
            'billing_period' => ['nullable', 'in:monthly,yearly'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ], [
            'email.required' => 'Email je obavezan.',
            'email.email' => 'Email mora biti validan.',
            'email.unique' => 'Tenant sa ovim email-om već postoji.',
            'phone.unique' => 'Tenant sa ovim telefonom već postoji.',
        ]);

        // Generate password if not provided
        if (empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Str::random(12);
            \Log::info('Password auto-generated for tenant', ['email' => $validated['email']]);
        }

        // Set defaults if not provided
        $validated['country'] = $validated['country'] ?? 'RS';
        $validated['timezone'] = $validated['timezone'] ?? 'Europe/Belgrade';
        $validated['currency'] = $validated['currency'] ?? 'RSD';

        // Log request data for debugging
        \Log::info('Creating tenant - request data', [
            'name' => $validated['name'] ?? 'MISSING',
            'email' => $validated['email'] ?? 'MISSING',
            'password' => isset($validated['password']) ? (strlen($validated['password']) > 0 ? '***' : 'EMPTY') : 'MISSING',
            'password_length' => isset($validated['password']) ? strlen($validated['password']) : 0,
            'all_keys' => array_keys($validated),
        ]);

        \Log::info('Creating tenant', ['data' => $validated]);

        try {
            $response = $this->apiService->createTenant($validated);

            \Log::info('API response', ['response' => $response]);

            if (!$response['success']) {
                \Log::error('Failed to create tenant', ['error' => $response['error'] ?? 'Unknown error', 'status' => $response['status'] ?? null]);
                return back()->withErrors(['error' => $response['error'] ?? 'Failed to create tenant']);
            }

            return redirect()->route('tenants.index')
                ->with('success', 'Tenant created successfully');
        } catch (\Exception $e) {
            \Log::error('Exception creating tenant', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $response = $this->apiService->getTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Tenant not found']);
        }

        $tenant = $response['data'] ?? [];

        // Load plans if tenant doesn't have subscription
        $plans = [];
        if (empty($tenant['subscription'])) {
            $plansResponse = $this->apiService->getPlans(['is_active' => true]);
            $plans = $plansResponse['success'] ? ($plansResponse['data'] ?? []) : [];
            
            // If data is paginated, extract the data array
            if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
                $plans = $plans['data'];
            }
        }

        return view('admin.tenants.show', [
            'tenant' => $tenant,
            'plans' => $plans,
        ]);
    }

    public function edit(string $id)
    {
        $response = $this->apiService->getTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Tenant not found']);
        }

        // Load plans from API
        $plansResponse = $this->apiService->getPlans(['is_active' => true]);
        $plans = $plansResponse['success'] ? ($plansResponse['data'] ?? []) : [];
        
        // If data is paginated, extract the data array
        if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
            $plans = $plans['data'];
        }

        return view('admin.tenants.edit', [
            'tenant' => $response['data'] ?? [],
            'plans' => $plans,
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
            'plan_id' => 'nullable|integer',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        // Separate tenant data from plan assignment
        $tenantData = array_filter($validated, function($key) {
            return !in_array($key, ['plan_id', 'billing_period']);
        }, ARRAY_FILTER_USE_KEY);

        // Update tenant if there are tenant fields to update
        if (!empty($tenantData)) {
            $response = $this->apiService->updateTenant((int) $id, $tenantData);

            if (!$response['success']) {
                return back()->withErrors(['error' => $response['error'] ?? 'Failed to update tenant']);
            }
        }

        // Assign plan if plan_id is provided
        if (isset($validated['plan_id'])) {
            $planResponse = $this->apiService->assignPlanToTenant(
                (int) $id,
                (int) $validated['plan_id'],
                $validated['billing_period'] ?? null
            );

            if (!$planResponse['success']) {
                return back()->withErrors(['error' => $planResponse['error'] ?? 'Failed to assign plan to tenant']);
            }
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

    public function destroy(string $id)
    {
        $response = $this->apiService->deleteTenant((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to delete tenant']);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deleted successfully');
    }
}
