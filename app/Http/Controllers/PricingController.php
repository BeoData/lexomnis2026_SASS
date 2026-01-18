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
        // #region agent log
        try {
            $logFile = 'c:' . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'LEXOMNIS PRO' . DIRECTORY_SEPARATOR . '.cursor' . DIRECTORY_SEPARATOR . 'debug.log';
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logData = [
                'id' => 'log_' . time() . '_' . uniqid(),
                'timestamp' => time() * 1000,
                'location' => 'PricingController.php:18',
                'message' => 'PricingController index called',
                'data' => ['request_url' => $request->fullUrl()],
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'A'
            ];
            @file_put_contents($logFile, json_encode($logData) . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            // Ignore logging errors
        }
        // #endregion

        // Get plans from Core App API
        // Note: This should call the public /api/subscriptions/plans endpoint
        // For now, we'll use the admin endpoint, but in production this should be public
        $response = $this->apiService->getPlans(['is_active' => true]);
        
        // #region agent log
        try {
            $logData2 = [
                'id' => 'log_' . time() . '_' . uniqid(),
                'timestamp' => time() * 1000,
                'location' => 'PricingController.php:35',
                'message' => 'API response received',
                'data' => [
                    'success' => $response['success'] ?? false,
                    'has_data' => isset($response['data']),
                    'data_type' => gettype($response['data'] ?? null),
                    'data_keys' => is_array($response['data'] ?? null) ? array_keys($response['data'] ?? []) : null,
                    'data_count' => is_array($response['data'] ?? null) ? count($response['data']) : 0,
                ],
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'A'
            ];
            @file_put_contents($logFile, json_encode($logData2) . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            // Ignore logging errors
        }
        // #endregion
        
        $plans = $response['success'] ? $response['data'] : [];

        // Handle paginated response
        if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
            $plans = $plans['data'];
        }

        // #region agent log
        try {
            $logData3 = [
                'id' => 'log_' . time() . '_' . uniqid(),
                'timestamp' => time() * 1000,
                'location' => 'PricingController.php:50',
                'message' => 'Plans extracted',
                'data' => [
                    'plans_count' => is_array($plans) ? count($plans) : 0,
                    'plans_type' => gettype($plans),
                    'is_paginated' => is_array($plans) && isset($plans['data']),
                    'sample_plan' => is_array($plans) && count($plans) > 0 ? [
                        'id' => $plans[0]['id'] ?? null,
                        'name' => $plans[0]['name'] ?? null,
                        'plan_key' => $plans[0]['plan_key'] ?? null,
                        'billing_period' => $plans[0]['billing_period'] ?? null,
                    ] : null,
                ],
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'A'
            ];
            @file_put_contents($logFile, json_encode($logData3) . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            // Ignore logging errors
        }
        // #endregion

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

        // #region agent log
        try {
            $logData4 = [
                'id' => 'log_' . time() . '_' . uniqid(),
                'timestamp' => time() * 1000,
                'location' => 'PricingController.php:95',
                'message' => 'Final grouped plans before render',
                'data' => [
                    'grouped_plans_count' => count($groupedPlans),
                    'grouped_plans_sample' => count($groupedPlans) > 0 ? [
                        'plan_key' => $groupedPlans[0]['plan_key'] ?? null,
                        'name' => $groupedPlans[0]['name'] ?? null,
                        'has_monthly' => !empty($groupedPlans[0]['monthly']),
                        'has_yearly' => !empty($groupedPlans[0]['yearly']),
                    ] : null,
                    'grouped_plans_json_sample' => count($groupedPlans) > 0 ? json_encode(array_slice($groupedPlans, 0, 1)) : null,
                ],
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'A'
            ];
            @file_put_contents($logFile, json_encode($logData4) . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            // Ignore logging errors
        }
        // #endregion

        $inertiaData = [
            'groupedPlans' => $groupedPlans,
        ];

        // #region agent log
        try {
            $logData5 = [
                'id' => 'log_' . time() . '_' . uniqid(),
                'timestamp' => time() * 1000,
                'location' => 'PricingController.php:115',
                'message' => 'Inertia render data',
                'data' => [
                    'inertia_data_keys' => array_keys($inertiaData),
                    'grouped_plans_in_inertia' => isset($inertiaData['groupedPlans']) ? count($inertiaData['groupedPlans']) : 0,
                ],
                'sessionId' => 'debug-session',
                'runId' => 'run1',
                'hypothesisId' => 'A'
            ];
            @file_put_contents($logFile, json_encode($logData5) . "\n", FILE_APPEND);
        } catch (\Exception $e) {
            // Ignore logging errors
        }
        // #endregion

        return Inertia::render('Pricing/Index', $inertiaData);
    }
}
