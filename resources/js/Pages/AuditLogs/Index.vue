<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Audit Logs</h1>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form @submit.prevent="filter" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search logs..."
                            class="border border-gray-300 rounded-md px-3 py-2"
                        />
                        <select
                            v-model="actionFilter"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option value="">All Actions</option>
                            <option value="tenant.created">Tenant Created</option>
                            <option value="tenant.updated">Tenant Updated</option>
                            <option value="tenant.suspended">Tenant Suspended</option>
                            <option value="tenant.activated">Tenant Activated</option>
                            <option value="user.created">User Created</option>
                            <option value="user.updated">User Updated</option>
                            <option value="user.suspended">User Suspended</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                        </select>
                        <input
                            v-model="dateFrom"
                            type="date"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        />
                        <input
                            v-model="dateTo"
                            type="date"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        />
                        <button
                            type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Audit Logs Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date/Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tenant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDateTime(log.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getActionColorClass(log.action),
                                        ]"
                                    >
                                        {{ formatAction(log.action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ log.user?.name || 'System' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ log.firm?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ log.description || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <Link
                                        :href="route('audit-logs.show', log.id)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="auditLogs.length === 0" class="text-center py-12">
                        <p class="text-gray-500">No audit logs found.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    auditLogs: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const actionFilter = ref(props.filters.action || '');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const filter = () => {
    router.get(route('audit-logs.index'), {
        search: search.value,
        action: actionFilter.value,
        date_from: dateFrom.value,
        date_to: dateTo.value,
    }, {
        preserveState: true,
    });
};

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

