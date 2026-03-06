<?php

namespace App\Http\Controllers;

use App\Services\TenantAppApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class TenantRegistrationController extends Controller
{
    protected TenantAppApiService $apiService;

    public function __construct(TenantAppApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * Display the registration form with plans
     */
    public function index(Request $request)
    {
        // Get plans from API
        $response = $this->apiService->getPlans(['is_active' => true]);

        $plans = $response['success'] ? ($response['data'] ?? []) : [];

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

        return Inertia::render('TenantRegistration/Register', [
            'groupedPlans' => $groupedPlans,
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
            'registration_type' => $request->input('registration_type')
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
            'registration_type' => ['required', 'in:trial,paid'],
            'plan_id' => ['required_if:registration_type,paid', 'nullable', 'integer'],
            'billing_period' => ['required_if:registration_type,paid', 'nullable', 'in:monthly,yearly'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'payment_method' => ['required_if:registration_type,paid', 'nullable', 'in:stripe,paypal'],
        ]);

        Log::debug('TenantRegistrationController: Validation passed', [
            'email' => $validated['email'] ?? null,
            'registration_type' => $validated['registration_type'] ?? null
        ]);

        // Combine first_name and last_name into name if not provided directly
        if (empty($validated['name']) && !empty($validated['first_name']) && !empty($validated['last_name'])) {
            $validated['name'] = trim($validated['first_name'] . ' ' . $validated['last_name']);
        }

        // Set defaults
        $validated['country'] = $validated['country'] ?? 'RS';
        $validated['timezone'] = $validated['timezone'] ?? 'Europe/Belgrade';
        $validated['currency'] = $validated['currency'] ?? 'RSD';

        try {
            // Call public API endpoint
            $baseUrl = \App\Models\Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $apiToken = \App\Models\Setting::getByKey('tenant_app_api_token') ?: config('services.tenant_app.api_token');

            $baseUrl = rtrim($baseUrl, '/');
            $apiUrl = "{$baseUrl}/api/public/tenants/register";

            Log::debug('TenantRegistrationController: Before API call', [
                'baseUrl' => $baseUrl,
                'apiUrl' => $apiUrl,
                'hasApiToken' => !empty($apiToken)
            ]);

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, $validated);

            $statusCode = $response->status();
            $responseBody = $response->body();
             
            Log::debug('TenantRegistrationController: API response received', [
                'statusCode' => $statusCode,
                'responseBody' => $responseBody
            ]);

            $data = $response->json();

            Log::debug('TenantRegistrationController: Response parsed', [
                'success' => $data['success'] ?? false,
                'hasError' => isset($data['error']),
                'error' => $data['error'] ?? null
            ]);

            if ($data['success'] ?? false) {
                // 1. Create user in SASS database (for billing, profile, upgrades)
                // This user is NOT a SuperAdmin, just a client.
                \App\Models\User::updateOrCreate(
                    ['email' => $validated['email']],
                    [
                        'name' => $validated['name'],
                        'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
                        // 'role' => 'client', // If you have roles in SASS
                    ]
                );

                if ($validated['registration_type'] === 'trial') {
                    return redirect()->route('tenant.register.success')
                        ->with('message', 'Registracija je uspešna! Vaš nalog za plaćanja i radno okruženje su kreirani.');
                } else {
                    if (isset($data['data']['payment_url'])) {
                        return redirect($data['data']['payment_url']);
                    } else {
                        return redirect('http://127.0.0.1:8001/login');
                    }
                }
            } else {
                return back()->withErrors([
                    'error' => $data['error'] ?? 'Došlo je do greške prilikom registracije.',
                ])->withInput();
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $response = $e->response;
            $errorData = $response?->json() ?? ['message' => $e->getMessage()];

            Log::debug('TenantRegistrationController: RequestException caught', [
                'statusCode' => $response?->status(),
                'errorData' => $errorData
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
                'class' => get_class($e)
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
            $baseUrl = \App\Models\Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $baseUrl = rtrim($baseUrl, '/');
            $apiUrl = "{$baseUrl}/api/public/tenants/verify-email";

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, ['token' => $token]);

            $data = $response->json();

            if ($data['success'] ?? false) {
                return redirect()->route('tenant.register.success')
                    ->with('message', 'Email je uspešno potvrđen! Vaš tenant je sada aktivan.');
            } else {
                return redirect()->route('tenant.register')
                    ->withErrors(['error' => $data['error'] ?? 'Nevažeći ili istekao verification token.']);
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
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
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $baseUrl = \App\Models\Setting::getByKey('tenant_app_url') ?: config('services.tenant_app.url');
            $baseUrl = rtrim($baseUrl, '/');
            $apiUrl = "{$baseUrl}/api/public/tenants/resend-verification";

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($apiUrl, $validated);

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
}
