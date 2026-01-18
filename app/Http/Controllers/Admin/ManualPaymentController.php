<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManualPaymentController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['firm_id']);
        $response = $this->apiService->getPendingManualPayments($filters);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to fetch payments']);
        }

        return Inertia::render('Admin/Payments/Index', [
            'payments' => $response['data']['data'] ?? $response['data'] ?? [],
            'pagination' => $response['data'] ?? [],
            'filters' => $filters,
        ]);
    }

    public function show(int $id)
    {
        $response = $this->apiService->getPayment($id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Payment not found']);
        }

        return Inertia::render('Admin/Payments/Show', [
            'payment' => $response['data'] ?? [],
        ]);
    }

    public function approve(int $id)
    {
        $response = $this->apiService->approvePayment($id);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to approve payment']);
        }

        return back()->with('success', 'Payment approved successfully');
    }

    public function reject(int $id, Request $request)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $response = $this->apiService->rejectPayment($id, $validated['reason']);

        if (!$response['success']) {
            return back()->withErrors(['error' => $response['error'] ?? 'Failed to reject payment']);
        }

        return back()->with('success', 'Payment rejected');
    }
}
