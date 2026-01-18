<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscription Management</h1>

                <!-- Current Subscription -->
                <div v-if="subscription" class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ subscription.plan?.name || 'No Plan' }}</h2>
                            <p class="text-sm text-gray-600 mt-1">
                                Status: 
                                <span :class="getStatusColor(subscription.status)" class="font-medium">
                                    {{ subscription.status }}
                                </span>
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">
                                €{{ subscription.plan?.price || 0 }}
                            </div>
                            <div class="text-sm text-gray-600">
                                / {{ subscription.plan?.billing_period || 'month' }}
                            </div>
                        </div>
                    </div>

                    <div v-if="subscription.trial_ends_at" class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800">
                            Trial ends: {{ formatDate(subscription.trial_ends_at) }}
                        </p>
                    </div>

                    <div v-if="subscription.ends_at" class="mb-4">
                        <p class="text-sm text-gray-600">
                            Subscription ends: {{ formatDate(subscription.ends_at) }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-4">
                        <button
                            v-if="subscription.status !== 'cancelled'"
                            @click="showUpgradeModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                        >
                            Upgrade Plan
                        </button>
                        <button
                            v-if="subscription.status !== 'cancelled'"
                            @click="showCancelModal = true"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                        >
                            Cancel Subscription
                        </button>
                    </div>
                </div>

                <div v-else class="bg-white rounded-lg shadow p-6 mb-6 text-center">
                    <p class="text-gray-600 mb-4">No active subscription found.</p>
                    <Link
                        :href="route('pricing')"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
                    >
                        Choose a Plan
                    </Link>
                </div>

                <!-- Payment History -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-900">Payment History</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Method
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="invoice in invoices.data || invoices" :key="invoice.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(invoice.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        €{{ invoice.amount }} {{ invoice.currency }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">
                                        {{ invoice.payment_method }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="getStatusColor(invoice.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ invoice.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!invoices.data && (!invoices || invoices.length === 0)">
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                        No payment history found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Upgrade Modal -->
                <div v-if="showUpgradeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showUpgradeModal = false">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upgrade Plan</h3>
                        <select v-model="selectedPlanId" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-4">
                            <option value="">Select a plan</option>
                            <option v-for="plan in availablePlans" :key="plan.id" :value="plan.id">
                                {{ plan.name }} - €{{ plan.price }}/month
                            </option>
                        </select>
                        <div class="flex space-x-4">
                            <button
                                @click="handleUpgrade"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg"
                            >
                                Upgrade
                            </button>
                            <button
                                @click="showUpgradeModal = false"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cancel Modal -->
                <div v-if="showCancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showCancelModal = false">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Subscription</h3>
                        <p class="text-gray-600 mb-4">Are you sure you want to cancel your subscription? You will lose access to premium features.</p>
                        <div class="flex space-x-4">
                            <button
                                @click="handleCancel"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg"
                            >
                                Yes, Cancel
                            </button>
                            <button
                                @click="showCancelModal = false"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg"
                            >
                                Keep Subscription
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    subscription: {
        type: Object,
        default: null,
    },
    invoices: {
        type: [Object, Array],
        default: () => [],
    },
    plans: {
        type: Array,
        default: () => [],
    },
});

const showUpgradeModal = ref(false);
const showCancelModal = ref(false);
const selectedPlanId = ref('');

const availablePlans = computed(() => {
    if (!props.subscription || !props.plans) return [];
    const currentPrice = props.subscription.plan?.price || 0;
    return props.plans.filter(plan => plan.price > currentPrice);
});

const getStatusColor = (status) => {
    const colors = {
        active: 'text-green-800 bg-green-100',
        trial: 'text-blue-800 bg-blue-100',
        cancelled: 'text-red-800 bg-red-100',
        suspended: 'text-yellow-800 bg-yellow-100',
        completed: 'text-green-800 bg-green-100',
        pending: 'text-yellow-800 bg-yellow-100',
        failed: 'text-red-800 bg-red-100',
    };
    return colors[status] || 'text-gray-800 bg-gray-100';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('sr-RS', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const handleUpgrade = () => {
    if (!selectedPlanId.value) return;
    
    router.post(route('subscriptions.upgrade'), {
        plan_id: selectedPlanId.value,
    }, {
        onSuccess: () => {
            showUpgradeModal.value = false;
        },
    });
};

const handleCancel = () => {
    router.post(route('subscriptions.cancel'), {}, {
        onSuccess: () => {
            showCancelModal.value = false;
        },
    });
};
</script>
