<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Settings</h1>

                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.errors?.settings" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    {{ $page.props.errors.settings }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- API Settings -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">API Configuration</h2>
                            <!-- Connection Status Badge -->
                            <div v-if="apiConnectionStatus" class="flex items-center gap-2">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': apiConnectionStatus.status === 'connected',
                                        'bg-red-100 text-red-800': apiConnectionStatus.status === 'disconnected' || apiConnectionStatus.status === 'error',
                                        'bg-yellow-100 text-yellow-800': apiConnectionStatus.status === 'not_configured',
                                    }"
                                    class="px-3 py-1 rounded-full text-sm font-medium flex items-center gap-2"
                                >
                                    <span v-if="apiConnectionStatus.status === 'connected'">✓</span>
                                    <span v-else-if="apiConnectionStatus.status === 'disconnected' || apiConnectionStatus.status === 'error'">✗</span>
                                    <span v-else>⚠</span>
                                    {{ getStatusText(apiConnectionStatus.status) }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-6">
                            Configure API connection to Tenant App
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="tenant_app_url" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tenant App URL
                                </label>
                                <input
                                    id="tenant_app_url"
                                    v-model="tenantAppUrl"
                                    type="url"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    :class="{ 'border-red-300': urlError }"
                                    placeholder="http://localhost:8000"
                                />
                                <p v-if="urlError" class="mt-1 text-xs text-red-600">{{ urlError }}</p>
                                <p v-else class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('tenant_app_url') }}
                                </p>
                            </div>

                            <div>
                                <label for="tenant_app_api_token" class="block text-sm font-medium text-gray-700 mb-1">
                                    API Token
                                    <span class="text-red-600">*</span>
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="tenant_app_api_token"
                                        v-model="tenantAppApiToken"
                                        :type="showToken ? 'text' : 'password'"
                                        class="flex-1 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        :class="{ 'border-red-300': tokenError }"
                                        placeholder="Enter API token"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showToken = !showToken"
                                        class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        {{ showToken ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                                <p v-if="tokenError" class="mt-1 text-xs text-red-600">{{ tokenError }}</p>
                                <p v-else class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('tenant_app_api_token') }}
                                </p>

                                <!-- Test Connection Button -->
                                <div class="mt-4 flex items-center gap-4">
                                    <button
                                        type="button"
                                        @click="testConnection"
                                        :disabled="testingConnection || !canTestConnection"
                                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        <span v-if="testingConnection">Testing...</span>
                                        <span v-else>Test Connection</span>
                                    </button>

                                    <!-- Test Connection Status -->
                                    <div v-if="connectionTestResult" class="flex items-center gap-2">
                                        <span
                                            :class="{
                                                'text-green-600': connectionTestResult.success,
                                                'text-red-600': !connectionTestResult.success,
                                            }"
                                            class="font-medium text-sm flex items-center gap-1"
                                        >
                                            <span v-if="connectionTestResult.success">✓</span>
                                            <span v-else>✗</span>
                                            {{ connectionTestResult.message }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Help Text -->
                                <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h3 class="font-semibold text-blue-900 mb-2 text-sm">How to get API Token:</h3>
                                    <div class="space-y-3 text-sm text-blue-800">
                                        <div>
                                            <p class="font-medium mb-1">Option 1: Through UI (Recommended)</p>
                                            <ol class="list-decimal list-inside space-y-1 ml-2">
                                                <li>Go to Tenant App: <a href="http://127.0.0.1:8000/settings/personal/api-token" target="_blank" class="text-blue-600 underline">http://127.0.0.1:8000/settings/personal/api-token</a></li>
                                                <li>Click "Generate New API Token"</li>
                                                <li>Enter a name (e.g., "Super Admin Token")</li>
                                                <li>Click "Generate Token"</li>
                                                <li><strong>⚠️ Copy the token immediately - it's shown only once!</strong></li>
                                                <li>Paste it in the API Token field above</li>
                                            </ol>
                                        </div>
                                        <div class="border-t border-blue-200 pt-3">
                                            <p class="font-medium mb-1">Option 2: Through Console (Alternative)</p>
                                            <ol class="list-decimal list-inside space-y-1 ml-2">
                                                <li>Go to Tenant App directory: <code class="bg-blue-100 px-1 rounded">C:\var\LexOmnisC</code></li>
                                                <li>Run command: <code class="bg-blue-100 px-1 rounded">php artisan db:seed --class=ApiTokenSeeder</code></li>
                                                <li>Copy the token that appears in the console</li>
                                                <li>Paste it in the API Token field above</li>
                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tenant_app_timeout" class="block text-sm font-medium text-gray-700 mb-1">
                                        Timeout (seconds)
                                    </label>
                                    <input
                                        id="tenant_app_timeout"
                                        v-model.number="tenantAppTimeout"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ getDescription('tenant_app_timeout') }}
                                    </p>
                                </div>

                                <div>
                                    <label for="tenant_app_retry_attempts" class="block text-sm font-medium text-gray-700 mb-1">
                                        Retry Attempts
                                    </label>
                                    <input
                                        id="tenant_app_retry_attempts"
                                        v-model.number="tenantAppRetryAttempts"
                                        type="number"
                                        min="0"
                                        max="10"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ getDescription('tenant_app_retry_attempts') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">General Settings</h2>

                        <div class="space-y-4">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Application Name
                                </label>
                                <input
                                    id="app_name"
                                    v-model="appName"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('app_name') }}
                                </p>
                            </div>

                            <div>
                                <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-1">
                                    Items Per Page
                                </label>
                                <input
                                    id="items_per_page"
                                    v-model.number="itemsPerPage"
                                    type="number"
                                    min="5"
                                    max="100"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('items_per_page') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            :disabled="form.processing || !isFormValid"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>Save Settings</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    groups: {
        type: Object,
        default: () => ({}),
    },
    apiConnectionStatus: {
        type: Object,
        default: null,
    },
});

const showToken = ref(false);
const testingConnection = ref(false);
const connectionTestResult = ref(null);
const urlError = ref('');
const tokenError = ref('');

// Initialize form with all settings
const allSettings = computed(() => {
    const settings = [];
    Object.values(props.groups).forEach(group => {
        group.forEach(setting => {
            settings.push({
                key: setting.key,
                value: setting.value,
            });
        });
    });
    return settings;
});

const form = useForm({
    settings: allSettings.value,
});

// Helper function to get setting value
const getSetting = (key) => {
    const setting = form.settings.find(s => s.key === key);
    if (!setting) {
        // Create setting if it doesn't exist
        form.settings.push({ key, value: '' });
        return form.settings.find(s => s.key === key);
    }
    return setting;
};

// Computed properties for form fields
const tenantAppUrl = computed({
    get: () => getSetting('tenant_app_url')?.value || '',
    set: (value) => {
        getSetting('tenant_app_url').value = value;
    },
});

const tenantAppApiToken = computed({
    get: () => getSetting('tenant_app_api_token')?.value || '',
    set: (value) => {
        getSetting('tenant_app_api_token').value = value;
    },
});

const tenantAppTimeout = computed({
    get: () => getSetting('tenant_app_timeout')?.value || '',
    set: (value) => {
        getSetting('tenant_app_timeout').value = value;
    },
});

const tenantAppRetryAttempts = computed({
    get: () => getSetting('tenant_app_retry_attempts')?.value || '',
    set: (value) => {
        getSetting('tenant_app_retry_attempts').value = value;
    },
});

const appName = computed({
    get: () => getSetting('app_name')?.value || '',
    set: (value) => {
        getSetting('app_name').value = value;
    },
});

const itemsPerPage = computed({
    get: () => getSetting('items_per_page')?.value || '',
    set: (value) => {
        getSetting('items_per_page').value = value;
    },
});

const getDescription = (key) => {
    const allSettings = Object.values(props.groups).flat();
    const setting = allSettings.find(s => s.key === key);
    return setting?.description || '';
};

const getStatusText = (status) => {
    const statusMap = {
        'connected': 'Connected',
        'disconnected': 'Disconnected',
        'error': 'Error',
        'not_configured': 'Not Configured',
    };
    return statusMap[status] || 'Unknown';
};

// Form validation
const isFormValid = computed(() => {
    const url = tenantAppUrl.value;
    const token = tenantAppApiToken.value;
    
    if (!url || !token) {
        return false;
    }
    
    try {
        new URL(url);
        return true;
    } catch {
        return false;
    }
});

const canTestConnection = computed(() => {
    const url = tenantAppUrl.value;
    const token = tenantAppApiToken.value;
    return url && token && !urlError.value && !tokenError.value;
});

// Real-time validation
watch(tenantAppUrl, (value) => {
    if (!value) {
        urlError.value = '';
        return;
    }
    
    try {
        new URL(value);
        urlError.value = '';
    } catch {
        urlError.value = 'Invalid URL format';
    }
});

watch(tenantAppApiToken, (value) => {
    if (!value) {
        tokenError.value = 'API Token is required';
    } else if (value.length < 10) {
        tokenError.value = 'API Token seems too short';
    } else {
        tokenError.value = '';
    }
});

const testConnection = async () => {
    if (testingConnection.value || !canTestConnection.value) {
        return;
    }

    testingConnection.value = true;
    connectionTestResult.value = null;

    try {
        const url = tenantAppUrl.value;
        const token = tenantAppApiToken.value;

        const response = await window.axios.post(route('settings.test-connection'), {
            url: url,
            token: token,
        });

        connectionTestResult.value = {
            success: response.data.success,
            message: response.data.message,
        };

        // Clear result after 5 seconds
        setTimeout(() => {
            connectionTestResult.value = null;
        }, 5000);
    } catch (error) {
        connectionTestResult.value = {
            success: false,
            message: error.response?.data?.message || 'Connection failed. Please check your settings.',
        };

        // Clear result after 5 seconds
        setTimeout(() => {
            connectionTestResult.value = null;
        }, 5000);
    } finally {
        testingConnection.value = false;
    }
};

const submit = () => {
    if (!isFormValid.value) {
        return;
    }
    form.put(route('settings.update'));
};
</script>
