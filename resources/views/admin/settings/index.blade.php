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
                    @endphp

                    <div>
                        <label for="tenant_app_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Tenant App URL
                        </label>
                        @php $urlSetting = $getSetting('tenant_app_url'); @endphp
                        <input
                            id="tenant_app_url"
                            name="settings[{{ $urlSetting->id ?? 'new' }}][key]"
                            type="hidden"
                            value="tenant_app_url"
                        />
                        <input
                            name="settings[{{ $urlSetting->id ?? 'new' }}][value]"
                            type="url"
                            value="{{ old('settings.'.$urlSetting->id.'.value', $urlSetting->value ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('settings.*.value') border-red-300 @enderror"
                            placeholder="http://localhost:8000"
                        />
                        @error('settings.*.value')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if($urlSetting)
                            <p class="mt-1 text-xs text-gray-500">{{ $urlSetting->description ?? '' }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="tenant_app_api_token" class="block text-sm font-medium text-gray-700 mb-1">
                            API Token <span class="text-red-600">*</span>
                        </label>
                        @php $tokenSetting = $getSetting('tenant_app_api_token'); @endphp
                        <input
                            id="tenant_app_api_token"
                            name="settings[{{ $tokenSetting->id ?? 'new' }}][key]"
                            type="hidden"
                            value="tenant_app_api_token"
                        />
                        <div class="flex gap-2">
                            <input
                                id="api_token_input"
                                name="settings[{{ $tokenSetting->id ?? 'new' }}][value]"
                                type="password"
                                value="{{ old('settings.'.$tokenSetting->id.'.value', $tokenSetting->value ?? '') }}"
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
                            <p class="mt-1 text-xs text-gray-500">{{ $tokenSetting->description ?? '' }}</p>
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
                                name="settings[{{ $timeoutSetting->id ?? 'new' }}][key]"
                                value="tenant_app_timeout"
                            />
                            <input
                                id="tenant_app_timeout"
                                name="settings[{{ $timeoutSetting->id ?? 'new' }}][value]"
                                type="number"
                                min="1"
                                value="{{ old('settings.'.$timeoutSetting->id.'.value', $timeoutSetting->value ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            @if($timeoutSetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $timeoutSetting->description ?? '' }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="tenant_app_retry_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                                Retry Attempts
                            </label>
                            @php $retrySetting = $getSetting('tenant_app_retry_attempts'); @endphp
                            <input
                                type="hidden"
                                name="settings[{{ $retrySetting->id ?? 'new' }}][key]"
                                value="tenant_app_retry_attempts"
                            />
                            <input
                                id="tenant_app_retry_attempts"
                                name="settings[{{ $retrySetting->id ?? 'new' }}][value]"
                                type="number"
                                min="0"
                                max="10"
                                value="{{ old('settings.'.$retrySetting->id.'.value', $retrySetting->value ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            />
                            @if($retrySetting)
                                <p class="mt-1 text-xs text-gray-500">{{ $retrySetting->description ?? '' }}</p>
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
                    @endphp

                    <div>
                        <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Application Name
                        </label>
                        @php $appNameSetting = $getGeneralSetting('app_name'); @endphp
                        <input
                            type="hidden"
                            name="settings[{{ $appNameSetting->id ?? 'new' }}][key]"
                            value="app_name"
                        />
                        <input
                            id="app_name"
                            name="settings[{{ $appNameSetting->id ?? 'new' }}][value]"
                            type="text"
                            value="{{ old('settings.'.$appNameSetting->id.'.value', $appNameSetting->value ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        @if($appNameSetting)
                            <p class="mt-1 text-xs text-gray-500">{{ $appNameSetting->description ?? '' }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-1">
                            Items Per Page
                        </label>
                        @php $itemsSetting = $getGeneralSetting('items_per_page'); @endphp
                        <input
                            type="hidden"
                            name="settings[{{ $itemsSetting->id ?? 'new' }}][key]"
                            value="items_per_page"
                        />
                        <input
                            id="items_per_page"
                            name="settings[{{ $itemsSetting->id ?? 'new' }}][value]"
                            type="number"
                            min="5"
                            max="100"
                            value="{{ old('settings.'.$itemsSetting->id.'.value', $itemsSetting->value ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        />
                        @if($itemsSetting)
                            <p class="mt-1 text-xs text-gray-500">{{ $itemsSetting->description ?? '' }}</p>
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

