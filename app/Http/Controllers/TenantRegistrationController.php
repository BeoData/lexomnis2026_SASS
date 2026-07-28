<?php

namespace App\Http\Controllers;

use App\Jobs\SyncTenantFromMain;
use App\Models\Setting;
use App\Models\User;
use App\Services\TenantAppApiService;
use App\Services\TenantRegistrySyncService;
use App\Support\TenantAppUrl;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TenantRegistrationController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(
        TenantAppApiService $apiService,
        private readonly TenantRegistrySyncService $tenantRegistry,
    ) {
        $this->apiService = $apiService;
    }

    /**
     * Display the registration form with plans
     */
    public function index(Request $request)
    {
        // Get active customer-facing plans from API.
        // Use the real plan packages returned by the tenant app.
        $response = $this->apiService->getPlans([
            'is_active' => true,
            'is_visible_to_customers' => true,
        ]);

        $plans = $response['success'] ? ($response['data'] ?? []) : [];

        // If data is paginated, extract the data array
        if (is_array($plans) && isset($plans['data']) && is_array($plans['data'])) {
            $plans = $plans['data'];
        }

        // Group plans by plan_key manually
        $groupedPlans = [];
        if (is_array($plans) && ! empty($plans)) {
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

        $initialRegistrationType = in_array($request->query('registration_type'), ['trial', 'paid'])
            ? $request->query('registration_type')
            : 'trial';

        $initialBillingPeriod = in_array($request->query('billing_period'), ['monthly', 'yearly'])
            ? $request->query('billing_period')
            : 'monthly';

        $initialPlanId = $request->query('plan_id');
        $initialPlanId = is_numeric($initialPlanId) ? (int) $initialPlanId : null;

        return Inertia::render('TenantRegistration/Register', [
            'groupedPlans' => $groupedPlans,
            'initial_registration_type' => $initialRegistrationType,
            'initial_billing_period' => $initialBillingPeriod,
            'initial_plan_id' => $initialPlanId,
            'paypal_enabled' => ! empty(config('services.paypal.client_id')) && ! empty(config('services.paypal.client_secret')),
        ]);
    }

    /**
     * Handle tenant registration
     */
    public function store(Request $request)
    {
        Log::debug('TenantRegistrationController: store() entry', [
            'has_name' => $request->has('name'),
            'has_first_name' => $request->has('first_name'),
            'registration_type' => $request->input('registration_type'),
        ]);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'], // Can be provided directly or constructed from first_name + last_name
            'first_name' => ['required_without:name', 'string', 'max:255'],
            'last_name' => ['required_without:name', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'timezone' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:3'],
            'vat_status' => ['required', 'in:registered,not_registered'],
            'registration_type' => ['required', 'in:trial,paid'],
            'plan_id' => ['required_if:registration_type,paid', 'nullable', 'integer'],
            'billing_period' => ['required_if:registration_type,paid', 'nullable', 'in:monthly,yearly'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'payment_method' => ['required_if:registration_type,paid', 'nullable', 'in:stripe,paypal'],
        ]);

        $validated['success_url'] = route('tenant.register.success');
        $validated['cancel_url'] = route('tenant.register');

        Log::debug('TenantRegistrationController: Validation passed', [
            'email' => $validated['email'] ?? null,
            'registration_type' => $validated['registration_type'] ?? null,
        ]);

        // Combine first_name and last_name into name if not provided directly
        if (empty($validated['name']) && ! empty($validated['first_name']) && ! empty($validated['last_name'])) {
            $validated['name'] = trim($validated['first_name'].' '.$validated['last_name']);
        }

        // Set defaults
        $validated['country'] = $validated['country'] ?? 'RS';
        $validated['timezone'] = $validated['timezone'] ?? 'Europe/Belgrade';
        $validated['currency'] = $validated['currency'] ?? 'RSD';

        // Ensure Stripe returns the checkout session ID so we can confirm payment when the user lands back on the SaaS success page.
        if (($validated['registration_type'] ?? '') === 'paid' && ($validated['payment_method'] ?? '') === 'stripe') {
            $validated['success_url'] = route('tenant.register.success').'?session_id={CHECKOUT_SESSION_ID}';
        }

        try {
            // Call public API endpoint
            $baseUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $apiToken = Setting::getByKey('tenant_app_api_token') ?: config('services.tenant_app.api_token');

            $baseUrl = TenantAppUrl::normalize($baseUrl);
            $apiUrl = "{$baseUrl}/api/public/tenants/register";

            Log::debug('TenantRegistrationController: Before API call', [
                'baseUrl' => $baseUrl,
                'apiUrl' => $apiUrl,
                'hasApiToken' => ! empty($apiToken),
            ]);

            // Ensure tenant app knows this request originates from the SaaS app
            $http = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'X-Saas-Registration' => '1',
            ]);

            // If an API token is configured, send it as a Bearer token
            if (! empty($apiToken)) {
                $http = $http->withToken($apiToken);
            }

            $response = $http->post($apiUrl, $validated);

            $statusCode = $response->status();
            $responseBody = $response->body();

            Log::debug('TenantRegistrationController: API response received', [
                'statusCode' => $statusCode,
                'responseBody' => $responseBody,
            ]);

            $data = $response->json();

            Log::debug('TenantRegistrationController: Response parsed', [
                'success' => $data['success'] ?? false,
                'hasError' => isset($data['error']),
                'error' => $data['error'] ?? null,
            ]);

            if ($response->successful() && ($data['success'] ?? false)) {
                $mainFirmId = (int) data_get($data, 'data.id');
                if ($mainFirmId > 0) {
                    $this->syncOrQueue($mainFirmId);
                }

                // 1. Create user in SASS database (for billing, profile, upgrades)
                // This user is NOT a SuperAdmin, just a client.
                $user = User::updateOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => $validated['name'],
                        'password' => Hash::make($request->input('password')),
                        'role' => 'client',
                    ]
                );

                Auth::login($user);
                $request->session()->regenerate();

                if ($validated['registration_type'] === 'trial') {
                    return redirect()->route('tenant.register.success')
                        ->with('message', 'Registracija je uspešna! Vaš nalog za plaćanja i radno okruženje su kreirani.');
                } else {
                    $paymentUrl = $data['data']['payment_url'] ?? $data['checkout_url'] ?? $data['data']['checkout_url'] ?? null;

                    if ($paymentUrl) {
                        // For external URLs like Stripe checkout, use Inertia::location() to avoid CORS issues
                        return Inertia::location($paymentUrl);
                    }

                    return back()->withErrors([
                        'error' => 'Registracija je uspešna, ali došlo je do greške pri inicijalizaciji plaćanja. Molimo pokušajte ponovo ili kontaktirajte podršku.',
                    ])->withInput();
                }
            } else {
                // Handle validation errors from Core App (422)
                if ($response->status() === 422) {
                    $errors = $data['errors'] ?? [];

                    return back()->withErrors($errors)->withInput();
                }

                return back()->withErrors([
                    'error' => $data['error'] ?? $data['message'] ?? 'Došlo je do greške prilikom registracije.',
                ])->withInput();
            }
        } catch (RequestException $e) {
            $response = $e->response;
            $errorData = $response?->json() ?? ['message' => $e->getMessage()];

            Log::debug('TenantRegistrationController: RequestException caught', [
                'statusCode' => $response?->status(),
                'errorData' => $errorData,
            ]);

            Log::error('Tenant registration failed', [
                'error' => $errorData,
                'status' => $response?->status(),
            ]);

            return back()->withErrors([
                'error' => $errorData['error'] ?? $errorData['message'] ?? 'Došlo je do greške prilikom registracije.',
            ])->withInput();
        } catch (\Exception $e) {
            Log::debug('TenantRegistrationController: General Exception caught', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
            ]);

            Log::error('Tenant registration exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Došlo je do greške prilikom registracije. Molimo pokušajte ponovo.',
            ])->withInput();
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token)
    {
        try {
            $baseUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $apiToken = Setting::getByKey('tenant_app_api_token') ?: config('services.tenant_app.api_token');
            $baseUrl = TenantAppUrl::normalize($baseUrl);
            $apiUrl = "{$baseUrl}/api/public/tenants/verify-email";

            $http = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
            if (! empty($apiToken)) {
                $http = $http->withToken($apiToken);
            }
            $response = $http->post($apiUrl, ['token' => $token]);

            $data = $response->json();

            if ($data['success'] ?? false) {
                return redirect()->route('tenant.register.success')
                    ->with('message', 'Email je uspešno potvrđen! Vaš tenant je sada aktivan.');
            } else {
                return redirect()->route('tenant.register')
                    ->withErrors(['error' => $data['error'] ?? 'Nevažeći ili istekao verification token.']);
            }
        } catch (RequestException $e) {
            $response = $e->response;
            $errorData = $response?->json() ?? ['message' => $e->getMessage()];

            Log::error('Email verification failed', [
                'error' => $errorData,
                'status' => $response?->status(),
            ]);

            return redirect()->route('tenant.register')
                ->withErrors(['error' => $errorData['error'] ?? 'Nevažeći ili istekao verification token.']);
        } catch (\Exception $e) {
            Log::error('Email verification exception', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('tenant.register')
                ->withErrors(['error' => 'Došlo je do greške prilikom verifikacije email-a.']);
        }
    }

    /**
     * Show registration success page and confirm Stripe checkout if present.
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $confirmationMessage = session('message');
        $confirmationError = null;

        if ($sessionId) {
            try {
                $result = $this->apiService->confirmStripeCheckoutSession($sessionId);

                if ($result['success'] ?? false) {
                    $confirmationMessage = $confirmationMessage ?: ($result['message'] ?? 'Plaćanje je potvrđeno i vaš paket je aktiviran.');
                } else {
                    $confirmationError = $result['error'] ?? 'Nije bilo moguće potvrditi plaćanje. Kontaktirajte podršku.';
                }
            } catch (\Exception $e) {
                Log::error('TenantRegistrationController: Stripe confirmation failed', [
                    'session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);

                $confirmationError = 'Nije bilo moguće potvrditi plaćanje. Molimo pokušajte ponovo ili kontaktirajte podršku.';
            }
        }

        $tenantAppUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');

        return Inertia::render('TenantRegistration/Success', [
            'tenantAppUrl' => TenantAppUrl::normalize($tenantAppUrl),
            'confirmation_message' => $confirmationMessage,
            'confirmation_error' => $confirmationError,
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $baseUrl = Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $apiToken = Setting::getByKey('tenant_app_api_token') ?: config('services.tenant_app.api_token');
            $baseUrl = TenantAppUrl::normalize($baseUrl);
            $apiUrl = "{$baseUrl}/api/public/tenants/resend-verification";

            $http = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);
            if (! empty($apiToken)) {
                $http = $http->withToken($apiToken);
            }
            $response = $http->post($apiUrl, $validated);

            $data = $response->json();

            if ($data['success'] ?? false) {
                return back()->with('message', 'Verification email je ponovo poslat!');
            } else {
                return back()->withErrors(['error' => $data['error'] ?? 'Došlo je do greške.']);
            }
        } catch (\Exception $e) {
            Log::error('Resend verification failed', [
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Došlo je do greške. Molimo pokušajte ponovo.']);
        }
    }

    private function syncOrQueue(int $mainFirmId): void
    {
        try {
            $this->tenantRegistry->syncByMainId($mainFirmId);
        } catch (\Throwable $exception) {
            Log::warning('Immediate public-registration tenant sync failed; retry queued', [
                'main_firm_id' => $mainFirmId,
                'error' => $exception->getMessage(),
            ]);

            try {
                SyncTenantFromMain::dispatch($mainFirmId);
            } catch (\Throwable $queueException) {
                Log::error('Unable to queue public-registration tenant sync retry', [
                    'main_firm_id' => $mainFirmId,
                    'error' => $queueException->getMessage(),
                ]);
            }
        }
    }
}
