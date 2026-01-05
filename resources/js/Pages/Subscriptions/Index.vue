<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscriptions</h1>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form @submit.prevent="filter" class="flex gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search subscriptions..."
                            class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                        />
                        <select
                            v-model="statusFilter"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                        </select>
                        <button
                            type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Subscriptions Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="subscription in subscriptions" :key="subscription.id">
                            <Link
                                :href="route('subscriptions.show', subscription.id)"
                                class="block hover:bg-gray-50 px-4 py-4 sm:px-6"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ subscription.tenant?.name || 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Plan: {{ subscription.plan?.name || 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-1">
                                                Started: {{ subscription.started_at ? new Date(subscription.started_at).toLocaleDateString() : 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span
                                            :class="{
                                                'bg-green-100 text-green-800': subscription.status === 'active',
                                                'bg-red-100 text-red-800': subscription.status === 'cancelled',
                                                'bg-yellow-100 text-yellow-800': subscription.status === 'expired',
                                            }"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ subscription.status }}
                                        </span>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ subscription.plan?.price || '0' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ subscription.plan?.billing_cycle || 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>

                <div v-if="subscriptions.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
                    <p class="text-gray-500">No subscriptions found</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subscriptions: {
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
const statusFilter = ref(props.filters.status || '');

const filter = () => {
    router.get(route('subscriptions.index'), {
        search: search.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
    });
};
</script>

