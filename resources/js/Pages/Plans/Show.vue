<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('plans.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Plans
                    </Link>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ plan.name }}</h1>
                    <Link
                        :href="route('plans.edit', plan.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Edit
                    </Link>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span
                                    :class="plan.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ plan.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                ${{ plan.price }} / {{ plan.billing_cycle === 'monthly' ? 'month' : 'year' }}
                            </dd>
                        </div>
                        <div v-if="plan.description">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ plan.description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Billing Cycle</dt>
                            <dd class="mt-1 text-sm text-gray-900 capitalize">{{ plan.billing_cycle }}</dd>
                        </div>
                        <div v-if="plan.features && plan.features.length > 0">
                            <dt class="text-sm font-medium text-gray-500">Features</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <ul class="list-disc list-inside">
                                    <li v-for="(feature, index) in plan.features" :key="index">
                                        {{ feature }}
                                    </li>
                                </ul>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ plan.created_at ? new Date(plan.created_at).toLocaleString() : 'N/A' }}
                            </dd>
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

defineProps({
    plan: {
        type: Object,
        required: true,
    },
});
</script>

