<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class SettingsController extends Controller
{
    public function index()
    {
        $groups = [
            'api' => Setting::where('group', 'api')->get(),
            'general' => Setting::where('group', 'general')->get(),
        ];

        // Check API connection status
        $apiConnectionStatus = $this->checkApiConnection();

        return view('admin.settings.index', [
            'groups' => $groups,
            'apiConnectionStatus' => $apiConnectionStatus,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        // Additional validation for API settings
        foreach ($validated['settings'] as $settingData) {
            if ($settingData['key'] === 'tenant_app_url' && !empty($settingData['value'])) {
                if (!filter_var($settingData['value'], FILTER_VALIDATE_URL)) {
                    return redirect()->back()
                        ->withErrors(['settings' => 'Invalid URL format for Tenant App URL.']);
                }
            }
            if ($settingData['key'] === 'tenant_app_api_token' && empty($settingData['value'])) {
                return redirect()->back()
                    ->withErrors(['settings' => 'API Token is required.']);
            }
        }

        foreach ($validated['settings'] as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            
            if ($setting) {
                $setting->value = $settingData['value'] ?? '';
                $setting->save();
            }
        }

        // Clear config cache if API settings changed
        if (isset($validated['settings'])) {
            $apiKeys = ['tenant_app_url', 'tenant_app_api_token', 'tenant_app_timeout', 'tenant_app_retry_attempts'];
            $hasApiChanges = collect($validated['settings'])->contains(function ($setting) use ($apiKeys) {
                return in_array($setting['key'], $apiKeys);
            });

            if ($hasApiChanges) {
                // Update .env file or config cache
                $this->updateEnvFile($validated['settings']);
            }
        }

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }

    protected function updateEnvFile(array $settings)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);
        
        foreach ($settings as $setting) {
            $key = strtoupper($setting['key']);
            $value = $setting['value'] ?? '';
            
            // Map setting keys to env keys
            $envKey = match($setting['key']) {
                'tenant_app_url' => 'TENANT_APP_URL',
                'tenant_app_api_token' => 'TENANT_APP_API_TOKEN',
                'tenant_app_timeout' => 'TENANT_APP_TIMEOUT',
                'tenant_app_retry_attempts' => 'TENANT_APP_RETRY_ATTEMPTS',
                default => null,
            };

            if ($envKey) {
                // Replace or add env variable
                if (preg_match("/^{$envKey}=.*/m", $envContent)) {
                    $envContent = preg_replace("/^{$envKey}=.*/m", "{$envKey}={$value}", $envContent);
                } else {
                    $envContent .= "\n{$envKey}={$value}";
                }
            }
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * Test API connection
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => 'required|url',
            'token' => 'required|string',
        ]);

        try {
            $url = rtrim($validated['url'], '/') . '/api/admin/system/health';
            $token = $validated['token'];

            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])
                ->get($url);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'status' => $response->status(),
                    'message' => 'Connection successful!',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'status' => $response->status(),
                'message' => $response->json()['message'] ?? 'Connection failed',
                'data' => $response->json(),
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check API connection status using current settings
     */
    protected function checkApiConnection(): array
    {
        try {
            $url = Setting::get('tenant_app_url');
            $token = Setting::get('tenant_app_api_token');

            if (!$url || !$token) {
                return [
                    'connected' => false,
                    'status' => 'not_configured',
                    'message' => 'API URL or Token not configured',
                ];
            }

            $apiUrl = rtrim($url, '/') . '/api/admin/system/health';

            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ])
                ->get($apiUrl);

            if ($response->successful()) {
                return [
                    'connected' => true,
                    'status' => 'connected',
                    'message' => 'API connection successful',
                ];
            }

            return [
                'connected' => false,
                'status' => 'disconnected',
                'message' => $response->json()['message'] ?? 'Connection failed',
            ];

        } catch (\Exception $e) {
            return [
                'connected' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
