<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Feature Flags</h1>
                    <Link
                        :href="route('feature-flags.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Create Feature Flag
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form @submit.prevent="filter" class="flex gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search feature flags..."
                            class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                        />
                        <select
                            v-model="isActiveFilter"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option value="">All Statuses</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <button
                            type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Feature Flags Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="flag in featureFlags" :key="flag.id">
                            <Link
                                :href="route('feature-flags.show', flag.id)"
                                class="block hover:bg-gray-50 px-4 py-4 sm:px-6"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ flag.name }}
                                            </p>
                                            <span
                                                :class="[
                                                    'ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    flag.is_active
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-gray-100 text-gray-800',
                                                ]"
                                            >
                                                {{ flag.is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-500">
                                            Key: <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">{{ flag.key }}</code>
                                        </p>
                                        <p v-if="flag.description" class="mt-1 text-sm text-gray-600">
                                            {{ flag.description }}
                                        </p>
                                        <p v-if="flag.tenant_id" class="mt-1 text-xs text-gray-400">
                                            Tenant ID: {{ flag.tenant_id }}
                                        </p>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-gray-400"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>

                    <div v-if="featureFlags.length === 0" class="text-center py-12">
                        <p class="text-gray-500">No feature flags found.</p>
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
    featureFlags: {
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
const isActiveFilter = ref(props.filters.is_active !== undefined ? String(props.filters.is_active) : '');

const filter = () => {
    router.get(route('feature-flags.index'), {
        search: search.value,
        is_active: isActiveFilter.value ? Number(isActiveFilter.value) : null,
    }, {
        preserveState: true,
    });
};
</script>

