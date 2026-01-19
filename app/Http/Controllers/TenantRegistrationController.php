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
        // #region agent log
        file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:59','message'=>'store() entry','data'=>['has_name'=>$request->has('name'),'has_first_name'=>$request->has('first_name'),'registration_type'=>$request->input('registration_type')],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
        // #endregion
        
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
            'plan_id' => ['required_if:registration_type,paid', 'nullable', 'integer', 'exists:plans,id'],
            'billing_period' => ['required_if:registration_type,paid', 'nullable', 'in:monthly,yearly'],
            'trial_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'payment_method' => ['required_if:registration_type,paid', 'nullable', 'in:stripe,paypal'],
        ]);

        // #region agent log
        file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_B','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:77','message'=>'Validation passed','data'=>['email'=>$validated['email']??null,'registration_type'=>$validated['registration_type']??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'B'])."\n", FILE_APPEND);
        // #endregion

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
            $baseUrl = config('services.tenant_app_api.base_url', env('TENANT_APP_API_URL', 'http://localhost:8000'));
            $apiToken = config('services.tenant_app_api.token', env('TENANT_APP_API_TOKEN', ''));
            
            // #region agent log
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:90','message'=>'Before API call','data'=>['baseUrl'=>$baseUrl,'apiUrl'=>"{$baseUrl}/api/public/tenants/register",'hasApiToken'=>!empty($apiToken)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
            // #endregion
            
            $client = new \GuzzleHttp\Client();
            $response = $client->post("{$baseUrl}/api/public/tenants/register", [
                'json' => $validated,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            // #region agent log
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            $response->getBody()->rewind();
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:102','message'=>'API response received','data'=>['statusCode'=>$statusCode,'responseBody'=>$responseBody],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
            // #endregion

            $data = json_decode($responseBody, true);

            // #region agent log
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:104','message'=>'Response parsed','data'=>['success'=>$data['success']??false,'hasError'=>isset($data['error']),'error'=>$data['error']??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
            // #endregion

            if ($data['success'] ?? false) {
                if ($validated['registration_type'] === 'trial') {
                    // Redirect to email verification page
                    return redirect()->route('tenant.register.success')
                        ->with('message', 'Registracija je uspešna! Molimo vas da proverite vaš email i potvrdite vašu email adresu.');
                } else {
                    // Redirect to payment URL
                    if (isset($data['data']['payment_url'])) {
                        return redirect($data['data']['payment_url']);
                    } else {
                        return redirect()->route('tenant.register.success')
                            ->with('message', 'Registracija je uspešna! Molimo vas da sačekate admin odobrenje.');
                    }
                }
            } else {
                return back()->withErrors([
                    'error' => $data['error'] ?? 'Došlo je do greške prilikom registracije.',
                ])->withInput();
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $errorData = json_decode($response->getBody()->getContents(), true);
            
            // #region agent log
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:123','message'=>'ClientException caught','data'=>['statusCode'=>$response->getStatusCode(),'errorData'=>$errorData],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
            // #endregion
            
            Log::error('Tenant registration failed', [
                'error' => $errorData,
                'status' => $response->getStatusCode(),
            ]);

            return back()->withErrors([
                'error' => $errorData['error'] ?? $errorData['message'] ?? 'Došlo je do greške prilikom registracije.',
            ])->withInput();
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // #region agent log
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_A','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:135','message'=>'RequestException caught','data'=>['message'=>$e->getMessage(),'hasResponse'=>$e->hasResponse()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n", FILE_APPEND);
            // #endregion
            
            Log::error('Tenant registration request exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'error' => 'Došlo je do greške prilikom registracije. Molimo pokušajte ponovo.',
            ])->withInput();
        } catch (\Exception $e) {
            // #region agent log
            file_put_contents('c:\var\LEXOMNIS PRO\.cursor\debug.log', json_encode(['id'=>'log_'.time().'_E','timestamp'=>time()*1000,'location'=>'TenantRegistrationController.php:135','message'=>'General Exception caught','data'=>['message'=>$e->getMessage(),'class'=>get_class($e)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n", FILE_APPEND);
            // #endregion
            
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
            $baseUrl = config('services.tenant_app_api.base_url', env('TENANT_APP_API_URL', 'http://localhost:8000'));
            
            $client = new \GuzzleHttp\Client();
            $response = $client->post("{$baseUrl}/api/public/tenants/verify-email", [
                'json' => ['token' => $token],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data['success'] ?? false) {
                return redirect()->route('tenant.register.success')
                    ->with('message', 'Email je uspešno potvrđen! Vaš tenant je sada aktivan.');
            } else {
                return redirect()->route('tenant.register')
                    ->withErrors(['error' => $data['error'] ?? 'Nevažeći ili istekao verification token.']);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $errorData = json_decode($response->getBody()->getContents(), true);
            
            Log::error('Email verification failed', [
                'error' => $errorData,
                'status' => $response->getStatusCode(),
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
            $baseUrl = config('services.tenant_app_api.base_url', env('TENANT_APP_API_URL', 'http://localhost:8000'));
            
            $client = new \GuzzleHttp\Client();
            $response = $client->post("{$baseUrl}/api/public/tenants/resend-verification", [
                'json' => $validated,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

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
