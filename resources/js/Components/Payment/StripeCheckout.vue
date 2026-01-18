<template>
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Card Details</h3>
        
        <div id="stripe-card-element" class="mb-4">
            <!-- Stripe Elements will mount here -->
        </div>
        
        <div id="stripe-card-errors" class="text-red-600 text-sm mb-4"></div>
        
        <button
            @click="handleSubmit"
            :disabled="processing"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-4 rounded-lg transition-colors"
        >
            <span v-if="processing">Processing...</span>
            <span v-else>Pay with Card</span>
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    planId: {
        type: Number,
        required: true,
    },
    billingPeriod: {
        type: String,
        default: 'monthly',
    },
});

const emit = defineEmits(['checkout-created']);

const processing = ref(false);
let stripe = null;
let cardElement = null;

onMounted(async () => {
    // Load Stripe.js
    if (!window.Stripe) {
        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.onload = initializeStripe;
        document.head.appendChild(script);
    } else {
        initializeStripe();
    }
});

const initializeStripe = () => {
    // Initialize Stripe with public key
    // In production, this should come from environment/config
    const stripeKey = 'pk_test_...'; // Replace with actual Stripe public key
    
    stripe = window.Stripe(stripeKey);
    const elements = stripe.elements();
    
    cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
        },
    });
    
    cardElement.mount('#stripe-card-element');
    cardElement.on('change', ({ error }) => {
        const displayError = document.getElementById('stripe-card-errors');
        if (error) {
            displayError.textContent = error.message;
        } else {
            displayError.textContent = '';
        }
    });
};

const handleSubmit = async () => {
    if (!stripe || !cardElement) {
        return;
    }

    processing.value = true;

    try {
        // Create checkout session via API
        const response = await fetch('/api/subscriptions/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                plan_id: props.planId,
                payment_method: 'stripe',
                billing_period: props.billingPeriod,
            }),
        });

        const data = await response.json();

        if (data.success && data.data.checkout_url) {
            window.location.href = data.data.checkout_url;
        } else {
            alert(data.error || 'Failed to create checkout session');
        }
    } catch (error) {
        console.error('Stripe checkout error:', error);
        alert('An error occurred. Please try again.');
    } finally {
        processing.value = false;
    }
};

onUnmounted(() => {
    if (cardElement) {
        cardElement.unmount();
    }
});
</script>
