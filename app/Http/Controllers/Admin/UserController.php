<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['firm_id', 'role', 'status', 'search']);
        $response = $this->apiService->getUsers($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch users']);
        }

        return Inertia::render('Users/Index', [
            'users' => $response['data']['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function show(string $id)
    {
        $response = $this->apiService->getUser((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'User not found']);
        }

        return Inertia::render('Users/Show', [
            'user' => $response['data'] ?? [],
        ]);
    }

    public function suspend(string $id)
    {
        $response = $this->apiService->suspendUser((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to suspend user']);
        }

        return back()->with('success', 'User suspended successfully');
    }

    public function resetPassword(string $id)
    {
        $response = $this->apiService->resetUserPassword((int) $id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to reset password']);
        }

        return back()->with('success', 'Password reset successfully. Temporary password: ' . ($response['data']['temporary_password'] ?? 'N/A'));
    }

    public function impersonate(string $id)
    {
        $response = $this->apiService->generateImpersonationToken((int) $id);

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
