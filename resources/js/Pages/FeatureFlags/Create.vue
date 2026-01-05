<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('feature-flags.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Feature Flags
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Feature Flag</h1>

                <div class="bg-white shadow rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="key" class="block text-sm font-medium text-gray-700">
                                    Key *
                                </label>
                                <input
                                    id="key"
                                    v-model="form.key"
                                    type="text"
                                    required
                                    placeholder="e.g., new_dashboard_feature"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Unique identifier for this feature flag (snake_case recommended)
                                </p>
                                <div v-if="errors.key" class="mt-1 text-sm text-red-600">
                                    {{ errors.key }}
                                </div>
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div>
                                <label for="environment" class="block text-sm font-medium text-gray-700">
                                    Environment *
                                </label>
                                <select
                                    id="environment"
                                    v-model="form.environment"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="all">All Environments</option>
                                    <option value="production">Production</option>
                                    <option value="staging">Staging</option>
                                    <option value="development">Development</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500">
                                    Select which environment(s) this feature flag applies to
                                </p>
                            </div>

                            <div>
                                <label for="tenant_id" class="block text-sm font-medium text-gray-700">
                                    Tenant ID (Optional)
                                </label>
                                <input
                                    id="tenant_id"
                                    v-model.number="form.tenant_id"
                                    type="number"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    Leave empty for global feature flag, or specify tenant ID for tenant-specific flag
                                </p>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <Link
                                :href="route('feature-flags.index')"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Feature Flag</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    name: '',
    key: '',
    description: '',
    is_active: true,
    tenant_id: null,
    environment: 'all',
});

const submit = () => {
    if (form.processing) {
        return;
    }

    form.post(route('feature-flags.store'), {
        onSuccess: () => {
            // Success handled by redirect
        },
        onError: (errors) => {
            console.error('Errors:', errors);
        },
    });
};
</script>

