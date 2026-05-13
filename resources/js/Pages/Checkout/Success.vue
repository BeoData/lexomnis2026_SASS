<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4" v-if="!loading">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4" v-else>
                    <svg class="animate-spin h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-2" v-if="!loading">
                    {{ confirmation_message || 'Payment Successful!' }}
                </h1>
                <h1 class="text-3xl font-bold text-gray-900 mb-2" v-else>
                    Confirming Payment...
                </h1>
                
                <p class="text-gray-600 mb-6" v-if="!loading">
                    {{ confirmation_message ? 'Your subscription has been activated successfully.' : 'Your subscription has been activated successfully.' }}
                </p>
                <p class="text-gray-600 mb-6" v-else>
                    Please wait while we confirm your payment...
                </p>
                
                <div class="text-red-600 mb-4" v-if="confirmation_error">
                    {{ confirmation_error }}
                </div>
                
                <div class="space-y-4">
                    <Link
                        :href="route('subscriptions.manage')"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition-colors"
                        :disabled="loading"
                    >
                        View Subscription
                    </Link>
                    <Link
                        :href="route('dashboard')"
                        class="block text-blue-600 hover:text-blue-800 font-medium"
                        :disabled="loading"
                    >
                        Go to Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const loading = ref(true);
const confirmation_message = ref('');
const confirmation_error = ref('');

onMounted(async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');

    if (!sessionId) {
        // Direct navigation without session_id — still show success
        loading.value = false;
        return;
    }

    try {
        const response = await axios.post('/checkout/confirm', {
            session_id: sessionId,
        }, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
        });

        if (response.data.success) {
            confirmation_message.value = response.data.message || 'Payment confirmed!';
        } else {
            confirmation_error.value = response.data.error || 'Failed to confirm payment.';
        }
    } catch (error) {
        console.error('Confirmation error:', error);
        confirmation_error.value = 'Failed to confirm payment. Please contact support.';
    }

    loading.value = false;
});
</script>
