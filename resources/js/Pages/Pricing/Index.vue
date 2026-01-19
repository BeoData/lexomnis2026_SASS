<template>
    <PublicLayout>
        <!-- Hero Section -->
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        Versatile plans. Powerful features. Simple pricing.
                    </h1>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Pick a plan that's right for your law firm. Streamline your firm operations with online time recording, billing, client accounting integrations, and more.
                    </p>
                </div>
            </div>
        </div>

        <!-- Billing Toggle -->
        <div class="bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-center mb-12">
                <div class="inline-flex rounded-lg border-2 border-gray-200 bg-white p-1 shadow-sm">
                    <button
                        @click="billingPeriod = 'monthly'"
                        :class="billingPeriod === 'monthly' 
                            ? 'bg-blue-600 text-white shadow-sm' 
                            : 'text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-3 rounded-md text-sm font-semibold transition-all duration-200"
                    >
                        Monthly
                    </button>
                    <button
                        @click="billingPeriod = 'yearly'"
                        :class="billingPeriod === 'yearly' 
                            ? 'bg-blue-600 text-white shadow-sm' 
                            : 'text-gray-700 hover:bg-gray-50'"
                        class="px-6 py-3 rounded-md text-sm font-semibold transition-all duration-200 relative"
                    >
                        Annually
                        <span 
                            v-if="billingPeriod === 'yearly'"
                            class="absolute -top-2 -right-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full font-bold"
                        >
                            Save {{ maxDiscount }}%
                        </span>
                    </button>
                </div>
            </div>

            <!-- Plans Grid -->
            <div v-if="groupedPlans.length === 0" class="text-center py-12">
                <p class="text-gray-600 text-lg">Nema dostupnih planova trenutno.</p>
            </div>
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="planGroup in groupedPlans"
                    :key="planGroup.plan_key"
                    :class="{
                        'ring-2 ring-blue-600 scale-105 shadow-xl': planGroup.metadata?.popular,
                        'ring-1 ring-gray-200 hover:shadow-lg': !planGroup.metadata?.popular,
                    }"
                    class="bg-white rounded-xl p-8 relative transition-all duration-200"
                >
                    <!-- Popular Badge -->
                    <div
                        v-if="planGroup.metadata?.popular"
                        class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-10"
                    >
                        <span class="bg-blue-600 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg">
                            Popular
                        </span>
                    </div>

                    <!-- Save Badge for Yearly -->
                    <div
                        v-if="billingPeriod === 'yearly' && planGroup.metadata?.discount_percentage"
                        class="absolute -top-4 right-4 z-10"
                    >
                        <span class="bg-green-500 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg">
                            Save over {{ planGroup.metadata.discount_percentage }}%
                        </span>
                    </div>

                    <!-- Plan Name -->
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ planGroup.name }}</h3>
                    </div>

                    <!-- Price -->
                    <div class="text-center mb-6">
                        <div class="flex items-baseline justify-center">
                            <span class="text-5xl font-bold text-gray-900">
                                {{ currencySymbol }}{{ currentPrice(planGroup) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-600 mt-1">
                            {{ currencyCode }} / user / {{ billingPeriod === 'monthly' ? 'month' : 'year' }}
                        </div>
                        <p v-if="billingPeriod === 'yearly' && currentPlan(planGroup)" class="mt-2 text-xs text-gray-500">
                            {{ currencySymbol }}{{ parseFloat(currentPlan(planGroup).price / 12).toFixed(2) }} per month billed annually
                        </p>
                    </div>

                    <!-- Tagline -->
                    <div v-if="planGroup.metadata?.tagline" class="mb-4 text-center">
                        <div class="inline-flex items-center text-sm text-gray-600 bg-blue-50 rounded-lg px-3 py-2">
                            <span class="text-lg mr-2">💡</span>
                            <span>{{ planGroup.metadata.tagline }}</span>
                        </div>
                    </div>

                    <!-- Highlight -->
                    <div v-if="planGroup.metadata?.highlight" class="mb-6 text-center">
                        <p class="text-base font-semibold text-gray-900">
                            {{ planGroup.metadata.highlight }}
                        </p>
                    </div>

                    <!-- Features List -->
                    <div class="mb-6 min-h-[200px]">
                        <ul class="space-y-3">
                            <li
                                v-for="feature in getFeatures(planGroup)"
                                :key="feature"
                                class="flex items-start text-sm text-gray-700"
                            >
                                <svg class="h-5 w-5 text-green-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ formatFeature(feature) }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- CTA Button -->
                    <Link
                        :href="`/checkout/${currentPlan(planGroup)?.id}?period=${billingPeriod}`"
                        :class="planGroup.metadata?.popular 
                            ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                            : 'bg-gray-900 hover:bg-gray-800 text-white'"
                        class="block w-full text-center px-6 py-3 rounded-lg font-semibold transition-colors shadow-md hover:shadow-lg"
                    >
                        {{ billingPeriod === 'yearly' ? 'Try for Free' : 'Try for Free' }}
                    </Link>

                    <!-- Add-ons Available -->
                    <div v-if="planGroup.metadata?.add_ons_available" class="mt-4 text-center">
                        <p class="text-xs text-gray-500">Add-ons available</p>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Additional Info Section -->
        <div class="bg-white border-t border-gray-200 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Enjoy with all plans</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-3xl mb-3">🔒</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Secure, reliable cloud software</h3>
                        <p class="text-sm text-gray-600">Securely access your firm's data with unlimited storage, encrypted backups, 2FA, and a 99.9% uptime guarantee.</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-3">🏆</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Award-winning 24/5 support</h3>
                        <p class="text-sm text-gray-600">Get the support you need, when you need it with unlimited support by phone, email, or live chat.</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-3">🔄</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Worry-free data migration</h3>
                        <p class="text-sm text-gray-600">Bring in everyone's data and get set up in a way that works for your firm.</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-3">💳</div>
                        <h3 class="font-semibold text-gray-900 mb-2">Industry-compliant online payments</h3>
                        <p class="text-sm text-gray-600">Accept client payments anytime, anywhere with competitive processing rates and no hidden fees.</p>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Pages/Layouts/PublicLayout.vue';

const props = defineProps({
    groupedPlans: {
        type: Array,
        default: () => [],
    },
});

const billingPeriod = ref('monthly');

const currentPlan = (planGroup) => {
    return billingPeriod.value === 'monthly' 
        ? planGroup.monthly 
        : planGroup.yearly;
};

const currentPrice = (planGroup) => {
    const plan = currentPlan(planGroup);
    if (!plan) return '0';
    
    if (billingPeriod.value === 'yearly') {
        return parseFloat(plan.price).toFixed(2);
    }
    return parseFloat(plan.price).toFixed(2);
};

const getFeatures = (planGroup) => {
    const plan = currentPlan(planGroup);
    if (!plan || !plan.features || !Array.isArray(plan.features)) {
        return [];
    }
    return plan.features.slice(0, 6); // Show first 6 features
};

const formatFeature = (feature) => {
    if (typeof feature !== 'string') return feature;
    return feature
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const maxDiscount = computed(() => {
    if (!props.groupedPlans || props.groupedPlans.length === 0) return 0;
    const discounts = props.groupedPlans
        .map(p => p.metadata?.discount_percentage)
        .filter(d => d !== null && d !== undefined);
    return discounts.length > 0 ? Math.max(...discounts) : 0;
});

const currencySymbol = computed(() => {
    // Get currency from first plan
    if (props.groupedPlans && props.groupedPlans.length > 0) {
        const plan = currentPlan(props.groupedPlans[0]);
        if (plan && plan.currency) {
            const currencies = {
                'USD': '$',
                'EUR': '€',
                'GBP': '£',
                'RSD': 'din',
            };
            return currencies[plan.currency] || '$';
        }
    }
    return '$';
});

const currencyCode = computed(() => {
    if (props.groupedPlans && props.groupedPlans.length > 0) {
        const plan = currentPlan(props.groupedPlans[0]);
        return plan?.currency || 'USD';
    }
    return 'USD';
});
</script>
