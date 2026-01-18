<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Manual Payment Requests</h1>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="filters.status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Payments Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Firm
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requested
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="payment in payments" :key="payment.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    #{{ payment.id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ payment.firm?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    €{{ payment.amount }} {{ payment.currency }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ payment.subscription?.plan?.name || 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusColor(payment.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ payment.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ formatDate(payment.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link
                                            :href="route('payments.manual.show', payment.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            View
                                        </Link>
                                        <button
                                            v-if="payment.status === 'pending'"
                                            @click="approvePayment(payment.id)"
                                            class="text-green-600 hover:text-green-900"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            v-if="payment.status === 'pending'"
                                            @click="openRejectModal(payment.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="payments.length === 0">
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    No manual payment requests found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Reject Modal -->
                <div v-if="showRejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showRejectModal = false">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reject Payment</h3>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                            <textarea
                                v-model="rejectReason"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md"
                                placeholder="Enter rejection reason..."
                            ></textarea>
                        </div>
                        <div class="flex space-x-4">
                            <button
                                @click="rejectPayment"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg"
                            >
                                Reject
                            </button>
                            <button
                                @click="showRejectModal = false; rejectPaymentId = null; rejectReason = ''"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    payments: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const showRejectModal = ref(false);
const rejectPaymentId = ref(null);
const rejectReason = ref('');

const getStatusColor = (status) => {
    const colors = {
        pending: 'text-yellow-800 bg-yellow-100',
        completed: 'text-green-800 bg-green-100',
        failed: 'text-red-800 bg-red-100',
    };
    return colors[status] || 'text-gray-800 bg-gray-100';
};

const formatDate = (date) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('sr-RS', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const approvePayment = (id) => {
    if (confirm('Are you sure you want to approve this payment?')) {
        router.post(route('payments.manual.approve', id), {}, {
            onSuccess: () => {
                // Refresh page
                router.reload();
            },
        });
    }
};

const openRejectModal = (id) => {
    rejectPaymentId.value = id;
    showRejectModal.value = true;
};

const rejectPayment = () => {
    if (!rejectReason.value.trim()) {
        alert('Please enter a rejection reason');
        return;
    }

    router.post(route('payments.manual.reject', rejectPaymentId.value), {
        reason: rejectReason.value,
    }, {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectPaymentId.value = null;
            rejectReason.value = '';
            router.reload();
        },
    });
};
</script>
