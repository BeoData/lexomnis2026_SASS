<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        $groups = [
            'api' => Setting::where('group', 'api')->get()->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'is_encrypted' => $setting->is_encrypted,
                ];
            }),
            'general' => Setting::where('group', 'general')->get()->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'key' => $setting->key,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'description' => $setting->description,
                    'is_encrypted' => $setting->is_encrypted,
                ];
            }),
        ];

        return Inertia::render('Settings/Index', [
            'groups' => $groups,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

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
}
