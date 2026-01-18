<template>
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Manual Payment</h3>
        
        <p class="text-sm text-gray-600 mb-4">
            Submit a payment request for manual approval. An administrator will review and process your payment.
        </p>
        
        <form @submit.prevent="handleSubmit">
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Notes (Optional)
                </label>
                <textarea
                    id="notes"
                    v-model="form.notes"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Add any additional information about your payment request..."
                ></textarea>
            </div>
            
            <button
                type="submit"
                :disabled="processing"
                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-medium py-3 px-4 rounded-lg transition-colors"
            >
                <span v-if="processing">Submitting Request...</span>
                <span v-else>Submit Payment Request</span>
            </button>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

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

const emit = defineEmits(['payment-requested']);

const processing = ref(false);

const form = useForm({
    notes: '',
});

const handleSubmit = () => {
    processing.value = true;
    
    emit('payment-requested', {
        notes: form.notes,
    });
    
    processing.value = false;
};
</script>
