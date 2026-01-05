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

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ featureFlag.name }}</h1>
                    <Link
                        :href="route('feature-flags.edit', featureFlag.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Edit
                    </Link>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Key</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <code class="bg-gray-100 px-2 py-1 rounded">{{ featureFlag.key }}</code>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        featureFlag.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800',
                                    ]"
                                >
                                    {{ featureFlag.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>

                        <div v-if="featureFlag.description">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ featureFlag.description }}</dd>
                        </div>

                        <div v-if="featureFlag.tenant_id">
                            <dt class="text-sm font-medium text-gray-500">Tenant ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ featureFlag.tenant_id }}</dd>
                        </div>

                        <div v-if="featureFlag.created_at">
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(featureFlag.created_at) }}</dd>
                        </div>

                        <div v-if="featureFlag.updated_at">
                            <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ formatDate(featureFlag.updated_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    featureFlag: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
};
</script>

