@extends('admin.layout')

@section('title', 'Settings - ' . config('app.name'))

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Settings</h1>

            <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- API Settings -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">API Configuration</h2>
                        @if($apiConnectionStatus)
                            <span class="px-3 py-1 rounded-full text-sm font-medium flex items-center gap-2
                                            {{ $apiConnectionStatus['status'] === 'connected' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ in_array($apiConnectionStatus['status'], ['disconnected', 'error']) ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $apiConnectionStatus['status'] === 'not_configured' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        ">
                                @if($apiConnectionStatus['status'] === 'connected') ✓
                                @elseif(in_array($apiConnectionStatus['status'], ['disconnected', 'error'])) ✗ @else ⚠ @endif
                                {{ ucfirst(str_replace('_', ' ', $apiConnectionStatus['status'])) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mb-6">
                        Configure API connection to Tenant App
                    </p>

                    <div class="space-y-4">
                        @php
                            $allSettings = collect($groups)->flatten();
                            $getSetting = function ($key) use ($allSettings) {
                                return $allSettings->firstWhere('key', $key);
                            };
                            $getSettingId = function ($setting, $key) {
                                if (!$setting)
                                    return $key;
                                return is_object($setting) ? $setting->id : (is_array($setting) ? ($setting['id'] ?? $key) : $key);
                            };
                            $getSettingValue = function ($setting) {
                                if (!$setting)
                                    return '';
                                return is_object($setting) ? ($setting->value ?? '') : (is_array($setting) ? ($setting['value'] ?? '') : '');
                            };
                            $getSettingDescription = function ($setting) {
                                if (!$setting)
                                    return '';
                                return is_object($setting) ? ($setting->description ?? '') : (is_array($setting) ? ($setting['description'] ?? '') : '');
                            };
                        @endphp

                        <div>
                            <label for="tenant_app_url" class="block text-sm font-medium text-gray-700 mb-1">
                                Tenant App URL
                            </label>
                            @php $urlSetting = $getSetting('tenant_app_url'); @endphp
                            <input id="tenant_app_url"
                                name="settings[{{ $getSettingId($urlSetting, 'tenant_app_url') }}][key]" type="hidden"
                                value="tenant_app_url" />
                            <input name="settings[{{ $getSettingId($urlSetting, 'tenant_app_url') }}][value]" type="url"
                                value="{{ old('settings.' . $getSettingId($urlSetting, 'tenant_app_url') . '.value', $getSettingValue($urlSetting)) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('settings.*.value') border-red-300 @enderror"
                                placeholder="http://localhost:8000" />
                            @error('settings.*.value')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($urlSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($urlSetting) }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="tenant_app_api_token" class="block text-sm font-medium text-gray-700 mb-1">
                                API Token <span class="text-red-600">*</span>
                            </label>
                            @php $tokenSetting = $getSetting('tenant_app_api_token'); @endphp
                            <input id="tenant_app_api_token"
                                name="settings[{{ $getSettingId($tokenSetting, 'tenant_app_api_token') }}][key]"
                                type="hidden" value="tenant_app_api_token" />
                            <div class="flex gap-2">
                                <input id="api_token_input"
                                    name="settings[{{ $getSettingId($tokenSetting, 'tenant_app_api_token') }}][value]"
                                    type="password"
                                    value="{{ old('settings.' . $getSettingId($tokenSetting, 'tenant_app_api_token') . '.value', $getSettingValue($tokenSetting)) }}"
                                    class="flex-1 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('settings.*.value') border-red-300 @enderror"
                                    placeholder="Enter API token" required />
                                <button type="button" onclick="toggleTokenVisibility()"
                                    class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <span id="toggle_text">Show</span>
                                </button>
                            </div>
                            @error('settings.*.value')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            @if($tokenSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($tokenSetting) }}</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="tenant_app_timeout" class="block text-sm font-medium text-gray-700 mb-1">
                                    Timeout (seconds)
                                </label>
                                @php $timeoutSetting = $getSetting('tenant_app_timeout'); @endphp
                                <input type="hidden"
                                    name="settings[{{ $getSettingId($timeoutSetting, 'tenant_app_timeout') }}][key]"
                                    value="tenant_app_timeout" />
                                <input id="tenant_app_timeout"
                                    name="settings[{{ $getSettingId($timeoutSetting, 'tenant_app_timeout') }}][value]"
                                    type="number" min="1"
                                    value="{{ old('settings.' . $getSettingId($timeoutSetting, 'tenant_app_timeout') . '.value', $getSettingValue($timeoutSetting)) }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                                @if($timeoutSetting)
                                    <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($timeoutSetting) }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="tenant_app_retry_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                                    Retry Attempts
                                </label>
                                @php $retrySetting = $getSetting('tenant_app_retry_attempts'); @endphp
                                <input type="hidden"
                                    name="settings[{{ $getSettingId($retrySetting, 'tenant_app_retry_attempts') }}][key]"
                                    value="tenant_app_retry_attempts" />
                                <input id="tenant_app_retry_attempts"
                                    name="settings[{{ $getSettingId($retrySetting, 'tenant_app_retry_attempts') }}][value]"
                                    type="number" min="0" max="10"
                                    value="{{ old('settings.' . $getSettingId($retrySetting, 'tenant_app_retry_attempts') . '.value', $getSettingValue($retrySetting)) }}"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                                @if($retrySetting)
                                    <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($retrySetting) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                <!-- Configuration & Blueprints -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Master Data & Blueprints</h2>
                    <p class="text-sm text-gray-600 mb-6">
                        Manage global system values and role templates assigned to all new tenants.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('settings.system.roles-permissions') }}" class="flex items-center p-4 border rounded-lg hover:bg-blue-50 hover:border-blue-200 transition-all group">
                            <div class="p-3 bg-blue-100 rounded-lg mr-4 group-hover:bg-blue-200">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Roles & Permissions</h3>
                                <p class="text-xs text-gray-500">View and manage system uloge and permisije templates.</p>
                            </div>
                        </a>
                        
                        <div class="flex items-center p-4 border rounded-lg bg-gray-50 opacity-60 cursor-not-allowed">
                            <div class="p-3 bg-gray-200 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-500">Global Lookups</h3>
                                <p class="text-xs text-gray-400">Coming soon: Manage global dictionaries.</p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- General Settings -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">General Settings</h2>

                    <div class="space-y-4">
                        @php
                            // Using consolidated helpers from above
                        @endphp

                        <div>
                            <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Application Name
                            </label>
                            @php $appNameSetting = $getSetting('app_name'); @endphp
                            <input type="hidden" name="settings[{{ $getSettingId($appNameSetting, 'app_name') }}][key]"
                                value="app_name" />
                            <input id="app_name" name="settings[{{ $getSettingId($appNameSetting, 'app_name') }}][value]"
                                type="text"
                                value="{{ old('settings.' . $getSettingId($appNameSetting, 'app_name') . '.value', $getSettingValue($appNameSetting)) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            @if($appNameSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($appNameSetting) }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-1">
                                Items Per Page
                            </label>
                            @php $itemsSetting = $getSetting('items_per_page'); @endphp
                            <input type="hidden" name="settings[{{ $getSettingId($itemsSetting, 'items_per_page') }}][key]"
                                value="items_per_page" />
                            <input id="items_per_page"
                                name="settings[{{ $getSettingId($itemsSetting, 'items_per_page') }}][value]" type="number"
                                min="5" max="100"
                                value="{{ old('settings.' . $getSettingId($itemsSetting, 'items_per_page') . '.value', $getSettingValue($itemsSetting)) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            @if($itemsSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($itemsSetting) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleTokenVisibility() {
            const input = document.getElementById('api_token_input');
            const toggleText = document.getElementById('toggle_text');
            if (input.type === 'password') {
                input.type = 'text';
                toggleText.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggleText.textContent = 'Show';
            }
        }
    </script>
@endsection