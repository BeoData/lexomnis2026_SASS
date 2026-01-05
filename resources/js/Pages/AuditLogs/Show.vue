<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('audit-logs.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Audit Logs
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Audit Log Details</h1>

                <div class="bg-white shadow rounded-lg p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ auditLog.id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Action</dt>
                            <dd class="mt-1">
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        getActionColorClass(auditLog.action),
                                    ]"
                                >
                                    {{ formatAction(auditLog.action) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">User</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ auditLog.user?.name || 'System' }}
                                <span v-if="auditLog.user?.email" class="text-gray-500">
                                    ({{ auditLog.user.email }})
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ auditLog.firm?.name || 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Model Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ auditLog.model_type || 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Model ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ auditLog.model_id || 'N/A' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ formatDateTime(auditLog.created_at) }}
                            </dd>
                        </div>

                        <div v-if="auditLog.description">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ auditLog.description }}</dd>
                        </div>
                    </dl>

                    <div v-if="auditLog.old_values || auditLog.new_values" class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Changes</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-if="auditLog.old_values">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Old Values</h4>
                                <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ JSON.stringify(auditLog.old_values, null, 2) }}</pre>
                            </div>
                            <div v-if="auditLog.new_values">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">New Values</h4>
                                <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ JSON.stringify(auditLog.new_values, null, 2) }}</pre>
                            </div>
                        </div>
                    </div>

                    <div v-if="auditLog.metadata" class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Metadata</h3>
                        <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ JSON.stringify(auditLog.metadata, null, 2) }}</pre>
                    </div>
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
    auditLog: {
        type: Object,
        required: true,
    },
});

const formatDateTime = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleString();
};

const formatAction = (action) => {
    if (!action) return 'Unknown';
    return action.replace(/\./g, ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const getActionColorClass = (action) => {
    if (!action) return 'bg-gray-100 text-gray-800';
    
    if (action.includes('created')) return 'bg-green-100 text-green-800';
    if (action.includes('updated')) return 'bg-blue-100 text-blue-800';
    if (action.includes('suspended') || action.includes('deleted')) return 'bg-red-100 text-red-800';
    if (action.includes('activated')) return 'bg-green-100 text-green-800';
    if (action === 'login') return 'bg-blue-100 text-blue-800';
    if (action === 'logout') return 'bg-gray-100 text-gray-800';
    
    return 'bg-gray-100 text-gray-800';
};
</script>

