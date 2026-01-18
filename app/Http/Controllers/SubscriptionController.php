<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    protected SubscriptionApiService $apiService;

    public function __construct(SubscriptionApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function manage(Request $request)
    {
        $subscriptionResponse = $this->apiService->getCurrentSubscription();
        $invoicesResponse = $this->apiService->getInvoices();
        
        $subscription = $subscriptionResponse['success'] ? $subscriptionResponse['data'] : null;
        $invoices = $invoicesResponse['success'] ? $invoicesResponse['data'] : [];

        // Get available plans for upgrade/downgrade
        $plansResponse = $this->apiService->getPlans();
        $plans = $plansResponse['success'] ? $plansResponse['data'] : [];

        return Inertia::render('Subscriptions/Manage', [
            'subscription' => $subscription,
            'invoices' => $invoices,
            'plans' => $plans,
        ]);
    }

    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
        ]);

        $response = $this->apiService->upgrade($validated);

        if (!$response['success']) {
            return back()->withErrors([
                'error' => $response['error'] ?? 'Failed to upgrade subscription',
            ]);
        }

        return back()->with('success', 'Subscription upgraded successfully');
    }

    public function cancel(Request $request)
    {
        $response = $this->apiService->cancel();

        if (!$response['success']) {
            return back()->withErrors([
                'error' => $response['error'] ?? 'Failed to cancel subscription',
            ]);
        }

        return back()->with('success', 'Subscription cancelled successfully');
    }
}
