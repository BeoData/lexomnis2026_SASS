<?php

namespace App\Http\Controllers;

use App\Services\SubscriptionApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    protected SubscriptionApiService $apiService;

    public function __construct(SubscriptionApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request, $plan)
    {
        $billingPeriod = $request->get('period', 'monthly');

        // Get plan details
        $plansResponse = $this->apiService->getPlans();
        $plans = $plansResponse['success'] ? $plansResponse['data'] : [];
        $selectedPlan = collect($plans)->firstWhere('id', $plan);

        if (!$selectedPlan) {
            return redirect()->route('pricing')
                ->with('error', 'Plan not found');
        }

        return Inertia::render('Checkout/Index', [
            'plan' => $selectedPlan,
            'billingPeriod' => $billingPeriod,
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer',
            'payment_method' => 'required|in:stripe,paypal,manual',
            'billing_period' => 'required|in:monthly,yearly',
            'success_url' => 'nullable|url',
            'cancel_url' => 'nullable|url',
        ]);

        $response = $this->apiService->createCheckout($validated);

        if (!$response['success']) {
            return back()->withErrors([
                'error' => $response['error'] ?? 'Failed to create checkout session',
            ]);
        }

        // Redirect to payment provider or return checkout data
        $checkoutData = $response['data']['data'] ?? $response['data'] ?? [];
        
        if (isset($checkoutData['checkout_url'])) {
            return redirect($checkoutData['checkout_url']);
        }

        return back()->with('checkout_data', $checkoutData);
    }

    public function success(Request $request)
    {
        return Inertia::render('Checkout/Success');
    }

    public function cancel(Request $request)
    {
        return Inertia::render('Checkout/Cancel');
    }
}
