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
                            @if($apiConnectionStatus['status'] === 'connected') ✓ @elseif(in_array($apiConnectionStatus['status'], ['disconnected', 'error'])) ✗ @else ⚠ @endif
                            {{ ucfirst(str_replace('_', ' ', $apiConnectionStatus['status'])) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Configure API connection to Tenant App
                </p>

                <div class="space-y-4">
                    @php
                        $apiSettings = $groups['api'] ?? collect();
                        $getSetting = function($key) use ($apiSettings) {
                            return $apiSettings->firstWhere('key', $key);
                        };
                        $getSettingId = function($setting) {
                            if (!$setting) return 'new';
                            return is_object($setting) ? $setting->id : (is_array($setting) ? ($setting['id'] ?? 'new') : 'new');
                        };
                        $getSettingValue = function($setting) {
                            if (!$setting) return '';
                            return is_object($setting) ? ($setting->value ?? '') : (is_array($setting) ? ($setting['value'] ?? '') : '');
                        };
                        $getSettingDescription = function($setting) {
                            if (!$setting) return '';
                            return is_object($setting) ? ($setting->description ?? '') : (is_array($setting) ? ($setting['description'] ?? '') : '');
                        };
                    @endphp

                    <div>
                        <label for="tenant_app_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Tenant App URL
                        </label>
                        @php $urlSetting = $getSetting('tenant_app_url'); @endphp
                        <input
                            id="tenant_app_url"
                            name="settings[{{ $getSettingId($urlSetting) }}][key]"
                            type="hidden"
                            value="tenant_app_url"
                        />
                        <input
                            name="settings[{{ $getSettingId($urlSetting) }}][value]"
                            type="url"
                            value="{{ old('settings.'.$getSettingId($urlSetting).'.value', $getSettingValue($urlSetting)) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('settings.*.value') border-red-300 @enderror"
                            placeholder="http://localhost:8000"
                        />
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
                        <input
                            id="tenant_app_api_token"
                            name="settings[{{ $getSettingId($tokenSetting) }}][key]"
                            type="hidden"
                            value="tenant_app_api_token"
                        />
                        <div class="flex gap-2">
                            <input
                                id="api_token_input"
                                name="settings[{{ $getSettingId($tokenSetting) }}][value]"
                                type="password"
                                value="{{ old('settings.'.$getSettingId($tokenSetting).'.value', $getSettingValue($tokenSetting)) }}"
                                class="flex-1 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('settings.*.value') border-red-300 @enderror"
                                placeholder="Enter API token"
                                required
                            />
                            <button
                                type="button"
                                onclick="toggleTokenVisibility()"
                                class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
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
                            <input
                                type="hidden"
                                name="settings[{{ $getSettingId($timeoutSetting) }}][key]"
                                value="tenant_app_timeout"
                            />
                            <input
                                id="tenant_app_timeout"
                                name="settings[{{ $getSettingId($timeoutSetting) }}][value]"
                                type="number"
                                min="1"
                                value="{{ old('settings.'.$getSettingId($timeoutSetting).'.value', $getSettingValue($timeoutSetting)) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            @if($timeoutSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($timeoutSetting) }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="tenant_app_retry_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                                Retry Attempts
                            </label>
                            @php $retrySetting = $getSetting('tenant_app_retry_attempts'); @endphp
                            <input
                                type="hidden"
                                name="settings[{{ $getSettingId($retrySetting) }}][key]"
                                value="tenant_app_retry_attempts"
                            />
                            <input
                                id="tenant_app_retry_attempts"
                                name="settings[{{ $getSettingId($retrySetting) }}][value]"
                                type="number"
                                min="0"
                                max="10"
                                value="{{ old('settings.'.$getSettingId($retrySetting).'.value', $getSettingValue($retrySetting)) }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            @if($retrySetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $getSettingDescription($retrySetting) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Settings -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">General Settings</h2>

                <div class="space-y-4">
                    @php
                        $generalSettings = $groups['general'] ?? collect();
                        $getGeneralSetting = function($key) use ($generalSettings) {
                            return $generalSettings->firstWhere('key', $key);
                        };
                        $getGeneralSettingId = function($setting) {
                            if (!$setting) return 'new';
                            return is_object($setting) ? $setting->id : (is_array($setting) ? ($setting['id'] ?? 'new') : 'new');
                        };
                        $getGeneralSettingValue = function($setting) {
                            if (!$setting) return '';
                            return is_object($setting) ? ($setting->value ?? '') : (is_array($setting) ? ($setting['value'] ?? '') : '');
                        };
                        $getGeneralSettingDescription = function($setting) {
                            if (!$setting) return '';
                            return is_object($setting) ? ($setting->description ?? '') : (is_array($setting) ? ($setting['description'] ?? '') : '');
                        };
                    @endphp

                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Application Name
                        </label>
                        @php $appNameSetting = $getGeneralSetting('app_name'); @endphp
                        <input
                            type="hidden"
                            name="settings[{{ $getGeneralSettingId($appNameSetting) }}][key]"
                            value="app_name"
                        />
                        <input
                            id="app_name"
                            name="settings[{{ $getGeneralSettingId($appNameSetting) }}][value]"
                            type="text"
                            value="{{ old('settings.'.$getGeneralSettingId($appNameSetting).'.value', $getGeneralSettingValue($appNameSetting)) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        @if($appNameSetting)
                            <p class="mt-1 text-xs text-gray-500">{{ $getGeneralSettingDescription($appNameSetting) }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-1">
                            Items Per Page
                        </label>
                        @php $itemsSetting = $getGeneralSetting('items_per_page'); @endphp
                        <input
                            type="hidden"
                            name="settings[{{ $getGeneralSettingId($itemsSetting) }}][key]"
                            value="items_per_page"
                        />
                        <input
                            id="items_per_page"
                            name="settings[{{ $getGeneralSettingId($itemsSetting) }}][value]"
                            type="number"
                            min="5"
                            max="100"
                            value="{{ old('settings.'.$getGeneralSettingId($itemsSetting).'.value', $getGeneralSettingValue($itemsSetting)) }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        @if($itemsSetting)
                            <p class="mt-1 text-xs text-gray-500">{{ $getGeneralSettingDescription($itemsSetting) }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                >
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

