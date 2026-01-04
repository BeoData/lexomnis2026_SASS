<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Settings</h1>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- API Settings -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">API Configuration</h2>
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
                                    v-model="form.settings.find(s => s.key === 'tenant_app_url')?.value"
                                    type="url"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="http://localhost:8000"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('tenant_app_url') }}
                                </p>
                            </div>

                            <div>
                                <label for="tenant_app_api_token" class="block text-sm font-medium text-gray-700 mb-1">
                                    API Token
                                </label>
                                <div class="flex gap-2">
                                    <input
                                        id="tenant_app_api_token"
                                        v-model="form.settings.find(s => s.key === 'tenant_app_api_token')?.value"
                                        :type="showToken ? 'text' : 'password'"
                                        class="flex-1 mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter API token"
                                    />
                                    <button
                                        type="button"
                                        @click="showToken = !showToken"
                                        class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        {{ showToken ? 'Hide' : 'Show' }}
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ getDescription('tenant_app_api_token') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="tenant_app_timeout" class="block text-sm font-medium text-gray-700 mb-1">
                                        Timeout (seconds)
                                    </label>
                                    <input
                                        id="tenant_app_timeout"
                                        v-model.number="form.settings.find(s => s.key === 'tenant_app_timeout')?.value"
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
                                        v-model.number="form.settings.find(s => s.key === 'tenant_app_retry_attempts')?.value"
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
                                    v-model="form.settings.find(s => s.key === 'app_name')?.value"
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
                                    v-model.number="form.settings.find(s => s.key === 'items_per_page')?.value"
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
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50"
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
import { ref, computed } from 'vue';

const props = defineProps({
    groups: {
        type: Object,
        default: () => ({}),
    },
});

const showToken = ref(false);

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

const getDescription = (key) => {
    const allSettings = Object.values(props.groups).flat();
    const setting = allSettings.find(s => s.key === key);
    return setting?.description || '';
};

const submit = () => {
    form.put(route('settings.update'));
};
</script>

