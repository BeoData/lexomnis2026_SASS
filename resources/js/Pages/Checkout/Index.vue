<template>
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Complete Your Purchase</h1>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                            
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">Plan</span>
                                        <span class="font-medium text-gray-900">{{ plan.name }}</span>
                                    </div>
                                    <div class="flex justify-between mb-2">
                                        <span class="text-gray-600">Billing Period</span>
                                        <span class="font-medium text-gray-900 capitalize">{{ billingPeriod }}</span>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4">
                                    <div class="flex justify-between text-lg font-bold text-gray-900">
                                        <span>Total</span>
                                        <span>€{{ calculateTotal }}</span>
                                    </div>
                                    <p v-if="billingPeriod === 'yearly'" class="text-sm text-gray-500 mt-1">
                                        Billed annually (save 10%)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-6">Select Payment Method</h2>

                            <!-- Payment Method Options -->
                            <div class="space-y-4 mb-6">
                                <label
                                    v-for="method in paymentMethods"
                                    :key="method.value"
                                    :class="selectedPaymentMethod === method.value ? 'ring-2 ring-blue-600 border-blue-600' : 'border-gray-300'"
                                    class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                >
                                    <input
                                        type="radio"
                                        v-model="selectedPaymentMethod"
                                        :value="method.value"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                    />
                                    <div class="ml-3 flex-1">
                                        <span class="font-medium text-gray-900">{{ method.label }}</span>
                                        <p class="text-sm text-gray-500 mt-1">{{ method.description }}</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Payment Method Details -->
                            <div v-if="selectedPaymentMethod === 'stripe'" class="mb-6 p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    You will be redirected to Stripe to complete your payment securely.
                                </p>
                            </div>

                            <div v-if="selectedPaymentMethod === 'paypal'" class="mb-6 p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    You will be redirected to PayPal to complete your payment securely.
                                </p>
                            </div>

                            <div v-if="selectedPaymentMethod === 'manual'" class="mb-6">
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Manual Payment</h3>
                                    <p class="text-sm text-gray-600 mb-4">
                                        Submit a payment request for manual approval. An administrator will review and process your payment.
                                    </p>
                                    <div class="mb-4">
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Additional Notes (Optional)
                                        </label>
                                        <textarea
                                            id="notes"
                                            v-model="manualNotes"
                                            rows="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Add any additional information about your payment request..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input
                                        type="checkbox"
                                        v-model="acceptedTerms"
                                        class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    />
                                    <span class="ml-2 text-sm text-gray-600">
                                        I agree to the
                                        <a href="#" class="text-blue-600 hover:text-blue-800">Terms of Service</a>
                                        and
                                        <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                                    </span>
                                </label>
                            </div>

                            <!-- Error Message -->
                            <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-sm text-red-800">{{ error }}</p>
                            </div>

                            <!-- Submit Button -->
                            <button
                                v-if="selectedPaymentMethod !== 'manual'"
                                @click="processPayment"
                                :disabled="!acceptedTerms || processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-4 rounded-lg transition-colors"
                            >
                                <span v-if="processing">Processing...</span>
                                <span v-else>Complete Payment</span>
                            </button>

                            <button
                                v-else
                                @click="processManualPayment"
                                :disabled="!acceptedTerms || processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-4 rounded-lg transition-colors"
                            >
                                <span v-if="processing">Submitting...</span>
                                <span v-else>Submit Payment Request</span>
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
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    plan: {
        type: Object,
        required: true,
    },
    billingPeriod: {
        type: String,
        default: 'monthly',
    },
});

const selectedPaymentMethod = ref('stripe');
const acceptedTerms = ref(false);
const error = ref(null);
const manualNotes = ref('');
const processing = ref(false);

const paymentMethods = [
    {
        value: 'stripe',
        label: 'Credit/Debit Card',
        description: 'Pay securely with Stripe',
    },
    {
        value: 'paypal',
        label: 'PayPal',
        description: 'Pay with your PayPal account',
    },
    {
        value: 'manual',
        label: 'Manual Payment',
        description: 'Request manual payment approval',
    },
];

const calculateTotal = computed(() => {
    let total = parseFloat(props.plan.price);
    if (props.billingPeriod === 'yearly') {
        total = total * 12 * 0.9; // 10% discount
    }
    return total.toFixed(2);
});

const processPayment = () => {
    if (!acceptedTerms.value) {
        error.value = 'Please accept the terms and conditions';
        return;
    }

    processing.value = true;
    error.value = null;

    router.post(route('checkout.process'), {
        plan_id: props.plan.id,
        payment_method: selectedPaymentMethod.value,
        billing_period: props.billingPeriod,
    }, {
        onSuccess: (page) => {
            if (page.props.checkout_data?.checkout_url) {
                window.location.href = page.props.checkout_data.checkout_url;
            }
        },
        onError: (errors) => {
            error.value = errors.error || 'Failed to create checkout session';
            processing.value = false;
        },
    });
};

const processManualPayment = () => {
    if (!acceptedTerms.value) {
        error.value = 'Please accept the terms and conditions';
        return;
    }

    processing.value = true;
    error.value = null;

    router.post(route('checkout.process'), {
        plan_id: props.plan.id,
        payment_method: 'manual',
        billing_period: props.billingPeriod,
        notes: manualNotes.value,
    }, {
        onSuccess: () => {
            router.visit(route('checkout.success'));
        },
        onError: (errors) => {
            error.value = errors.error || 'Failed to create payment request';
            processing.value = false;
        },
    });
};
</script>
