<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['tenant_id', 'role', 'status', 'search']);

        $tenantsResponse = $this->apiService->getTenants(['per_page' => 100]);
        $tenants = $tenantsResponse['data']['data'] ?? [];

        $users = [];
        $pagination = [];
        if (!empty($filters['tenant_id'])) {
            $response = $this->apiService->getUsers($filters);

            if (!$response['success']) {
                return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch users']);
            }

            $users = $response['data']['data'] ?? [];
            $pagination = $response['data'] ?? [];
        }

        return view('admin.users.index', [
            'users' => $users,
            'pagination' => $pagination,
            'filters' => $filters,
            'tenants' => $tenants,
        ]);
    }

    public function show(string $id)
    {
        $tenantId = request('tenant_id') ? (int) request('tenant_id') : null;
        $response = $this->apiService->getUser((int) $id, $tenantId);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'User not found']);
        }

        return view('admin.users.show', [
            'user' => $response['data'] ?? [],
            'tenant_id' => $tenantId,
        ]);
    }

    public function suspend(string $id)
    {
        $tenantId = request('tenant_id') ? (int) request('tenant_id') : null;
        $response = $this->apiService->suspendUser((int) $id, $tenantId);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to suspend user']);
        }

        return back()->with('success', 'User suspended successfully');
    }

    public function resetPassword(string $id)
    {
        $tenantId = request('tenant_id') ? (int) request('tenant_id') : null;
        $response = $this->apiService->resetUserPassword((int) $id, $tenantId);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to reset password']);
        }

        return back()->with('success', 'Password reset successfully. Temporary password: ' . ($response['data']['temporary_password'] ?? 'N/A'));
    }

    public function impersonate(string $id)
    {
        $tenantId = request('tenant_id') ? (int) request('tenant_id') : null;
        $response = $this->apiService->generateImpersonationToken((int) $id, false, $tenantId);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to generate impersonation token']);
        }

        $token = $response['data']['token'] ?? null;
        $impersonateUrl = $response['data']['impersonate_url'] ?? null;

        if ($impersonateUrl) {
            return redirect($impersonateUrl);
        }

        return back()->with('success', 'Impersonation token generated. Use the token to access the tenant account.');
    }
}
