<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">
                    Super Admin Dashboard
                </h1>
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Tenants Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Tenants</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ stats?.tenants?.total || 0 }}
                                </p>
                                <div class="mt-4 flex gap-4 text-sm">
                                    <span class="text-green-600">
                                        Active: {{ stats?.tenants?.active || 0 }}
                                    </span>
                                    <span class="text-yellow-600">
                                        Trial: {{ stats?.tenants?.trial || 0 }}
                                    </span>
                                    <span class="text-red-600">
                                        Suspended: {{ stats?.tenants?.suspended || 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-blue-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <Link :href="route('tenants.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                            View all tenants →
                        </Link>
                    </div>

                    <!-- Users Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ stats?.users?.total || 0 }}
                                </p>
                                <div class="mt-4 flex gap-4 text-sm">
                                    <span class="text-green-600">
                                        Active: {{ stats?.users?.active || 0 }}
                                    </span>
                                    <span class="text-red-600">
                                        Suspended: {{ stats?.users?.suspended || 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-green-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                        <Link :href="route('users.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                            View all users →
                        </Link>
                    </div>

                    <!-- Subscriptions Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Subscriptions</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ stats?.subscriptions?.total || 0 }}
                                </p>
                                <div class="mt-4 flex gap-4 text-sm">
                                    <span class="text-green-600">
                                        Active: {{ stats?.subscriptions?.active || 0 }}
                                    </span>
                                    <span class="text-red-600">
                                        Expired: {{ stats?.subscriptions?.expired || 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-purple-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <Link :href="route('subscriptions.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                            View all subscriptions →
                        </Link>
                    </div>

                    <!-- Plans Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Plans</p>
                                <p class="text-3xl font-bold text-gray-900 mt-2">
                                    {{ stats?.plans?.total || 0 }}
                                </p>
                                <div class="mt-4 flex gap-4 text-sm">
                                    <span class="text-green-600">
                                        Active: {{ stats?.plans?.active || 0 }}
                                    </span>
                                </div>
                            </div>
                            <div class="bg-yellow-100 rounded-full p-3">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                        </div>
                        <Link :href="route('plans.index')" class="mt-4 inline-block text-sm text-blue-600 hover:text-blue-800">
                            View all plans →
                        </Link>
                    </div>
                </div>

                <!-- System Status -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">System Status</h3>
                                <div class="flex items-center gap-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                          :class="stats?.system?.status === 'healthy' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ stats?.system?.status === 'healthy' ? '✓ Healthy' : '✗ Unhealthy' }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                                          :class="stats?.system?.api_connected ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                        {{ stats?.system?.api_connected ? '✓ API Connected' : '✗ API Disconnected' }}
                                    </span>
                                </div>
                            </div>
                            <Link :href="route('system.index')" class="text-sm text-blue-600 hover:text-blue-800">
                                View details →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mb-8">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="recentActivity && recentActivity.length > 0" class="space-y-4">
                                <div v-for="activity in recentActivity" :key="activity.id" class="flex items-start gap-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ activity.description || activity.action }}</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ formatDate(activity.created_at) }}
                                            <span v-if="activity.user" class="ml-2">by {{ activity.user.name || activity.user.email }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-8 text-gray-500">
                                <p>No recent activity</p>
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <Link :href="route('audit-logs.index')" class="text-sm text-blue-600 hover:text-blue-800">
                                View all audit logs →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Link
                        :href="route('tenants.create')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">Create Tenant</h2>
                        <p class="text-gray-600">Add a new tenant to the system</p>
                    </Link>
                    <Link
                        :href="route('plans.create')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">Create Plan</h2>
                        <p class="text-gray-600">Create a new subscription plan</p>
                    </Link>
                    <Link
                        :href="route('feature-flags.create')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">Create Feature Flag</h2>
                        <p class="text-gray-600">Add a new feature flag</p>
                    </Link>
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

defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentActivity: {
        type: Array,
        default: () => [],
    },
});

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>
