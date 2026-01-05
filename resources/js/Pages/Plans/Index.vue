<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Plans</h1>
                    <Link
                        :href="route('plans.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Create Plan
                    </Link>
                </div>

                <!-- Plans Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ plan.name }}</h3>
                                <p v-if="plan.description" class="text-sm text-gray-600 mt-1">
                                    {{ plan.description }}
                                </p>
                            </div>
                            <span
                                :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            >
                                {{ plan.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <div class="text-3xl font-bold text-gray-900">
                                ${{ plan.price }}
                            </div>
                            <div class="text-sm text-gray-600">
                                / {{ plan.billing_cycle === 'monthly' ? 'month' : 'year' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Features:</h4>
                            <ul v-if="plan.features" class="text-sm text-gray-600 space-y-1">
                                <li v-for="(feature, index) in plan.features" :key="index">
                                    • {{ feature }}
                                </li>
                            </ul>
                            <p v-else class="text-sm text-gray-500">No features defined</p>
                        </div>

                        <div class="flex space-x-2">
                            <Link
                                :href="route('plans.show', plan.id)"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded text-center text-sm"
                            >
                                View
                            </Link>
                            <Link
                                :href="route('plans.edit', plan.id)"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center text-sm"
                            >
                                Edit
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="plans.length === 0" class="text-center py-12">
                    <p class="text-gray-500">No plans found</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    plans: {
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
</script>

