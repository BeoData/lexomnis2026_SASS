<template>
    <div
        :class="{
            'ring-2 ring-blue-600': isSelected,
            'hover:shadow-lg': true,
            'border-blue-500': isSelected,
        }"
        class="border rounded-lg p-6 cursor-pointer transition-all bg-white relative"
        @click="$emit('select', planGroup)"
    >
        <!-- Popular Badge -->
        <span
            v-if="planGroup.metadata?.popular && selectedBillingPeriod === 'monthly'"
            class="absolute top-4 right-4 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
        >
            Popular
        </span>

        <!-- Save Badge for Yearly -->
        <span
            v-if="selectedBillingPeriod === 'yearly' && planGroup.yearly?.metadata?.discount_percentage"
            class="absolute top-4 right-4 bg-green-100 text-green-800 text-xs px-2 py-1 rounded"
        >
            Save over {{ planGroup.yearly.metadata.discount_percentage }}%
        </span>

        <!-- Plan Name -->
        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ planGroup.name }}</h3>

        <!-- Price -->
        <div class="mb-4">
            <div class="text-3xl font-bold text-gray-900">
                ${{ currentPlan?.price || '0' }}
                <span class="text-base font-normal text-gray-500">USD</span>
            </div>
            <div class="text-sm text-gray-500">user / month</div>
        </div>

        <!-- Tagline -->
        <div class="text-sm text-gray-600 mb-3">
            <span class="text-lg">💡</span> {{ planGroup.metadata?.tagline || '' }}
        </div>

        <!-- Highlight -->
        <div class="text-base font-medium text-gray-900 mb-4">
            {{ planGroup.metadata?.highlight || '' }}
        </div>

        <!-- Features List -->
        <ul class="space-y-2 mb-4">
            <li
                v-for="(feature, index) in features"
                :key="index"
                class="text-sm text-gray-600 flex items-start"
            >
                <span class="text-green-500 mr-2">✓</span>
                <span>{{ feature }}</span>
            </li>
        </ul>

        <!-- Add-ons Available -->
        <div v-if="planGroup.metadata?.add_ons_available" class="text-xs text-gray-500 mt-4">
            + Add-ons available
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    planGroup: {
        type: Object,
        required: true,
    },
    selectedBillingPeriod: {
        type: String,
        default: 'monthly',
        validator: (value) => ['monthly', 'yearly'].includes(value),
    },
    isSelected: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['select']);

const currentPlan = computed(() => {
    return props.selectedBillingPeriod === 'monthly'
        ? props.planGroup.monthly
        : props.planGroup.yearly;
});

const features = computed(() => {
    // Extract features from description or metadata
    const plan = currentPlan.value;
    if (!plan) return [];

    // Try to get features from plan features array
    if (plan.features && Array.isArray(plan.features)) {
        return plan.features.slice(0, 5); // Limit to 5 features for display
    }

    // Fallback: parse description for bullet points
    const desc = plan.description || '';
    const lines = desc.split('\n').filter(line => line.trim().startsWith('•') || line.trim().startsWith('-'));
    return lines.slice(0, 5).map(line => line.replace(/^[•-]\s*/, '').trim());
});
</script>
