<?php

namespace App\Http\Controllers;

use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function index(Request $request)
    {
        // Get plans from Core App API
        // Note: This should call the public /api/subscriptions/plans endpoint
        // For now, we'll use the admin endpoint, but in production this should be public
        $response = $this->apiService->getPlans(['is_active' => true]);
        
        $plans = $response['success'] ? $response['data'] : [];

        // Handle paginated response
        if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
            $plans = $plans['data'];
        }

        // Group plans by plan_key for Clio-style display
        $groupedPlans = [];
        if (is_array($plans) && !empty($plans)) {
            $grouped = collect($plans)->groupBy('plan_key')->map(function ($group, $planKey) {
                $first = $group->first();
                $monthly = $group->firstWhere('billing_period', 'monthly');
                $yearly = $group->firstWhere('billing_period', 'yearly');
                
                // Convert to array if needed
                $monthlyArray = is_object($monthly) ? $monthly->toArray() : (is_array($monthly) ? $monthly : null);
                $yearlyArray = is_object($yearly) ? $yearly->toArray() : (is_array($yearly) ? $yearly : null);
                $firstArray = is_object($first) ? $first->toArray() : (is_array($first) ? $first : []);
                
                // Calculate discount percentage for yearly
                $discountPercentage = null;
                if ($monthlyArray && $yearlyArray && isset($monthlyArray['price']) && $monthlyArray['price'] > 0) {
                    $yearlyMonthlyPrice = $yearlyArray['price'] / 12;
                    $discountPercentage = round((($monthlyArray['price'] - $yearlyMonthlyPrice) / $monthlyArray['price']) * 100);
                }
                
                return [
                    'plan_key' => $planKey,
                    'name' => $firstArray['name'] ?? '',
                    'monthly' => $monthlyArray,
                    'yearly' => $yearlyArray,
                    'metadata' => array_merge($firstArray['metadata'] ?? [], [
                        'discount_percentage' => $discountPercentage,
                    ]),
                ];
            })->values()->toArray();
            
            $groupedPlans = $grouped;
        }

        return Inertia::render('Pricing/Index', [
            'groupedPlans' => $groupedPlans,
        ]);
    }
}
