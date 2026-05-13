<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ClientDashboardController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Client dashboard - overview of profile, subscription, payments
     */
    public function dashboard()
    {
        $user = Auth::user();
        $tenantAppUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');

        $subscription = null;
        $invoices = [];

        try {
            // Use the API service instead of raw Http
            // Try searching by email
            $response = $this->apiService->getTenants([
                'search' => $user->email,
                'filter[email]' => $user->email, // Common in Spatie/QueryBuilder
                'email' => $user->email, // Legacy
                'include' => 'subscription,plan', // Try to eager load
                'per_page' => 1,
            ]);

            Log::debug('Client Dashboard: Subscription API response', [
                'success' => $response['success'] ?? false,
                'user_email' => $user->email
            ]);

            if ($response['success'] ?? false) {
                $payload = $response['data'] ?? [];
                // Handle different paginated formats
                $tenants = $payload['data'] ?? $payload ?? [];
                
                if (!empty($tenants) && is_array($tenants)) {
                    $tenant = isset($tenants[0]) ? $tenants[0] : reset($tenants);
                    
                    if ($tenant) {
                        Log::info('Client Dashboard: Found tenant', ['tenant_id' => $tenant['id'] ?? 'N/A']);
                        $subscription = $tenant['subscription'] ?? $tenant['active_subscription'] ?? null;

                        // Fallback: Always try to enrich subscription details with the tenant admin API
                        if (isset($tenant['id'])) {
                            $subRes = $this->apiService->getSubscriptions([
                                'firm_id' => $tenant['id'],
                                'per_page' => 1,
                            ]);

                            if ($subRes['success'] ?? false) {
                                $subs = $subRes['data']['data'] ?? $subRes['data'] ?? [];
                                $tenantSubscription = !empty($subs) ? (isset($subs[0]) ? $subs[0] : reset($subs)) : null;
                                if ($tenantSubscription) {
                                    $subscription = $tenantSubscription;
                                    $invoices = $tenantSubscription['payment_transactions'] ?? $tenantSubscription['paymentTransactions'] ?? [];
                                }
                            }
                        }
                    }
                }
            } else {
                Log::warning('Client Dashboard: API call for tenants failed', [
                    'error' => $response['error'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Client dashboard: Critical failure fetching subscription info', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return view('client.dashboard', [
            'user' => $user,
            'subscription' => $subscription,
            'invoices' => $invoices,
            'tenantAppUrl' => rtrim($tenantAppUrl, '/'),
        ]);
    }

    /**
     * Show client profile page
     */
    public function profile()
    {
        $user = Auth::user();

        return view('client.profile', [
            'user' => $user,
        ]);
    }

    /**
     * Update client profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password if provided
        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password']) || !Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Trenutna lozinka nije ispravna.']);
            }
            $user->password = $validated['new_password'];
        }

        $user->save();

        return back()->with('success', 'Profil je uspešno ažuriran.');
    }

    /**
     * Show subscription management page
     */
    public function subscription()
    {
        $user = Auth::user();
        $tenantAppUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');

        $subscription = null;
        $plans = [];
        $invoices = [];
        $tenantId = null;

        try {
            // 1. Find the tenant record for this user by email
            $subRes = $this->apiService->getTenants([
                'search'         => $user->email,
                'filter[email]'  => $user->email,
                'include'        => 'subscription,plan',
                'per_page'       => 1,
            ]);

            if ($subRes['success'] ?? false) {
                $payload = $subRes['data'] ?? [];
                $tenants = $payload['data'] ?? $payload ?? [];
                if (!empty($tenants) && is_array($tenants)) {
                    $tenant = isset($tenants[0]) ? $tenants[0] : reset($tenants);
                    if ($tenant) {
                        $tenantId = $tenant['id'] ?? null;
                        $subscription = $tenant['subscription'] ?? $tenant['active_subscription'] ?? null;

                        // 2. Get the most recent subscription via the subscriptions API
                        if ($tenantId) {
                            $fallbackSubRes = $this->apiService->getSubscriptions([
                                'tenant_id' => $tenantId,
                                'per_page'  => 1,
                                'include'   => 'plan,paymentTransactions',
                            ]);

                            if ($fallbackSubRes['success'] ?? false) {
                                $fallbackSubs = $fallbackSubRes['data']['data'] ?? $fallbackSubRes['data'] ?? [];
                                $tenantSubscription = !empty($fallbackSubs) ? (isset($fallbackSubs[0]) ? $fallbackSubs[0] : reset($fallbackSubs)) : null;

                                if ($tenantSubscription) {
                                    $subscription = $tenantSubscription;

                                    // Try to pull invoices from nested relationship first
                                    $invoices = $tenantSubscription['payment_transactions']
                                        ?? $tenantSubscription['paymentTransactions']
                                        ?? [];

                                    // 3a. If still empty, query payments by subscription ID
                                    if (empty($invoices) && isset($tenantSubscription['id'])) {
                                        $payRes = $this->apiService->getPaymentsBySubscription((int) $tenantSubscription['id']);
                                        if ($payRes['success'] ?? false) {
                                            $invoices = $payRes['data']['data'] ?? $payRes['data'] ?? [];
                                        }
                                    }
                                }
                            }

                            // 3b. Fallback: query transactions directly by tenant ID
                            if (empty($invoices)) {
                                $payRes = $this->apiService->getPaymentTransactionsByTenant((int) $tenantId);
                                if ($payRes['success'] ?? false) {
                                    $invoices = $payRes['data']['data'] ?? $payRes['data'] ?? [];
                                }
                            }
                        }
                    }
                }
            }

            // 4. Get plans for upgrade options
            $plansResponse = $this->apiService->getPlans(['is_active' => true]);
            if ($plansResponse['success'] ?? false) {
                $plansData = $plansResponse['data'] ?? [];
                if (is_array($plansData) && isset($plansData['data'])) {
                    $plansData = $plansData['data'];
                }
                $plans = $plansData;
            }
        } catch (\Exception $e) {
            Log::warning('Client subscription: Could not fetch data', [
                'error' => $e->getMessage(),
            ]);
        }

        return view('client.subscription', [
            'user'         => $user,
            'subscription' => $subscription,
            'plans'        => $plans,
            'invoices'     => $invoices,
            'tenantAppUrl' => rtrim($tenantAppUrl, '/'),
        ]);
    }
}
