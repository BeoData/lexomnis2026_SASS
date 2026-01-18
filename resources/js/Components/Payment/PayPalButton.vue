<template>
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pay with PayPal</h3>
        
        <div id="paypal-button-container" class="mb-4">
            <!-- PayPal button will mount here -->
        </div>
        
        <p class="text-sm text-gray-600">
            You will be redirected to PayPal to complete your payment.
        </p>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

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

onMounted(async () => {
    // Load PayPal SDK
    if (!window.paypal) {
        const script = document.createElement('script');
        script.src = 'https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=EUR';
        script.onload = initializePayPal;
        document.head.appendChild(script);
    } else {
        initializePayPal();
    }
});

const initializePayPal = async () => {
    try {
        // Create checkout session first
        const response = await fetch('/api/subscriptions/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                plan_id: props.planId,
                payment_method: 'paypal',
                billing_period: props.billingPeriod,
            }),
        });

        const data = await response.json();

        if (data.success && data.data.checkout_url) {
            // Render PayPal button
            window.paypal.Buttons({
                createOrder: function(data, actions) {
                    // Use the checkout URL from our API
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '50.00', // This should come from plan data
                                currency: 'EUR'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Redirect to success page
                        window.location.href = '/checkout/success?payment_id=' + data.orderID + '&PayerID=' + data.payerID;
                    });
                },
                onError: function(err) {
                    console.error('PayPal error:', err);
                    alert('An error occurred with PayPal. Please try again.');
                }
            }).render('#paypal-button-container');
        } else {
            console.error('Failed to create PayPal checkout:', data.error);
        }
    } catch (error) {
        console.error('PayPal initialization error:', error);
    }
};
</script>
