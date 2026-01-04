<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">System Monitoring</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Health Status -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">System Health</h2>
                        <div v-if="health">
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span
                                        :class="health.status === 'healthy' ? 'text-green-600' : 'text-red-600'"
                                        class="text-sm font-medium"
                                    >
                                        {{ health.status || 'Unknown' }}
                                    </span>
                                </div>
                                <div v-if="health.version" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Version:</span>
                                    <span class="text-sm text-gray-900">{{ health.version }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Unable to fetch health status
                        </div>
                        <Link
                            :href="route('system.health')"
                            class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm"
                        >
                            View Details →
                        </Link>
                    </div>

                    <!-- Metrics -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Performance Metrics</h2>
                        <div v-if="metrics">
                            <div class="space-y-2">
                                <div v-if="metrics.response_time" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Response Time:</span>
                                    <span class="text-sm text-gray-900">{{ metrics.response_time }}ms</span>
                                </div>
                                <div v-if="metrics.memory_usage" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Memory Usage:</span>
                                    <span class="text-sm text-gray-900">{{ metrics.memory_usage }}MB</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Unable to fetch metrics
                        </div>
                        <Link
                            :href="route('system.metrics')"
                            class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm"
                        >
                            View Details →
                        </Link>
                    </div>

                    <!-- Queue Status -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Queue Status</h2>
                        <div v-if="queues">
                            <div class="space-y-2">
                                <div v-if="queues.pending" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Pending:</span>
                                    <span class="text-sm text-gray-900">{{ queues.pending }}</span>
                                </div>
                                <div v-if="queues.failed" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Failed:</span>
                                    <span class="text-sm text-red-600">{{ queues.failed }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Unable to fetch queue status
                        </div>
                    </div>

                    <!-- Cron Status -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4">Cron Status</h2>
                        <div v-if="crons">
                            <div class="space-y-2">
                                <div v-if="crons.last_run" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Last Run:</span>
                                    <span class="text-sm text-gray-900">
                                        {{ new Date(crons.last_run).toLocaleString() }}
                                    </span>
                                </div>
                                <div v-if="crons.status" class="flex justify-between">
                                    <span class="text-sm text-gray-600">Status:</span>
                                    <span
                                        :class="crons.status === 'running' ? 'text-green-600' : 'text-yellow-600'"
                                        class="text-sm font-medium"
                                    >
                                        {{ crons.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-gray-500">
                            Unable to fetch cron status
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    health: {
        type: Object,
        default: null,
    },
    metrics: {
        type: Object,
        default: null,
    },
    queues: {
        type: Object,
        default: null,
    },
    crons: {
        type: Object,
        default: null,
    },
});
</script>

