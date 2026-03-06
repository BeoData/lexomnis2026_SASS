<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('tenants.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Tenants
                    </Link>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ tenant.name }}</h1>
                    <div class="flex space-x-2">
                        <button
                            v-if="tenant.status === 'active'"
                            @click="impersonateTenant"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded flex items-center gap-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                            </svg>
                            Uloguj se kao Klijent
                        </button>
                        <Link
                            :href="route('tenants.edit', tenant.id)"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Edit
                        </Link>
                        <button
                            v-if="tenant.status === 'active'"
                            @click="suspendTenant"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Suspend
                        </button>
                        <button
                            v-if="tenant.status === 'suspended'"
                            @click="activateTenant"
                            class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Activate
                        </button>
                    </div>
                </div>

                <!-- Current Plan Section - Always Visible and Highlighted -->
                <div class="bg-white shadow-lg rounded-lg p-6 mb-6 border-2 border-blue-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Current Plan</h2>
                        <Link
                            :href="route('tenants.edit', tenant.id)"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded text-sm transition-all shadow-sm"
                        >
                            {{ tenant.subscription ? 'Change Plan' : 'Assign Plan' }}
                        </Link>
                    </div>

                    <!-- Plan Card - Always Visible -->
                    <div v-if="tenant.subscription && tenant.subscription.plan" 
                         class="border-2 border-blue-500 rounded-lg p-6 bg-gradient-to-br from-blue-50 to-white relative shadow-md">
                        <!-- Active Badge -->
                        <div class="absolute top-4 right-4">
                            <span
                                :class="{
                                    'bg-green-500 text-white': tenant.subscription?.status === 'active',
                                    'bg-yellow-500 text-white': tenant.subscription?.status === 'trial',
                                    'bg-red-500 text-white': tenant.subscription?.status === 'suspended',
                                }"
                                class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
                            >
                                {{ tenant.subscription?.status?.toUpperCase() || 'N/A' }}
                            </span>
                        </div>

                        <!-- Plan Name and Price -->
                        <div class="mb-4 pr-24">
                            <div v-if="tenant.subscription.status === 'trial'" class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                                    ⏱️ Trial Period - Besplatno
                                </span>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">
                                {{ tenant.subscription.plan.name || 'N/A' }}
                                <span v-if="tenant.subscription.status === 'trial'" class="text-lg text-gray-500 font-normal">
                                    (Plan nakon trial-a)
                                </span>
                            </h3>
                            <div class="flex items-baseline">
                                <span v-if="tenant.subscription.status === 'trial'" class="text-2xl font-bold text-gray-400 line-through mr-2">
                                    ${{ formatPrice(tenant.subscription.plan.price) }}
                                </span>
                                <span :class="{
                                    'text-4xl font-bold text-green-600': tenant.subscription.status === 'trial',
                                    'text-4xl font-bold text-blue-600': tenant.subscription.status !== 'trial'
                                }">
                                    <span v-if="tenant.subscription.status === 'trial'">$0.00</span>
                                    <span v-else>${{ formatPrice(tenant.subscription.plan.price) }}</span>
                                </span>
                                <span class="text-lg text-gray-500 ml-2">
                                    <span v-if="tenant.subscription.status === 'trial'">trial period</span>
                                    <span v-else>USD / {{ getBillingPeriodLabel(tenant.subscription.plan.billing_period) }}</span>
                                </span>
                            </div>
                            <div v-if="tenant.subscription.status === 'trial' && tenant.subscription.trial_ends_at" class="mt-2 text-sm text-gray-600">
                                <strong>Nakon {{ new Date(tenant.subscription.trial_ends_at).toLocaleDateString('sr-RS', { day: 'numeric', month: 'long', year: 'numeric' }) }}:</strong>
                                <span class="ml-1">${{ formatPrice(tenant.subscription.plan.price) }} USD / {{ getBillingPeriodLabel(tenant.subscription.plan.billing_period) }}</span>
                            </div>
                        </div>

                        <!-- Plan Description -->
                        <div v-if="tenant.subscription.plan.description" class="text-gray-600 mb-4">
                            {{ tenant.subscription.plan.description }}
                        </div>

                        <!-- Plan Features -->
                        <div v-if="tenant.subscription.plan.features && Array.isArray(tenant.subscription.plan.features) && tenant.subscription.plan.features.length > 0" 
                             class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Plan Features:</h4>
                            <ul class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <li
                                    v-for="(feature, index) in tenant.subscription.plan.features"
                                    :key="index"
                                    class="text-sm text-gray-600 flex items-start"
                                >
                                    <span class="text-green-500 mr-2 font-bold">✓</span>
                                    <span>{{ feature }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Plan Metadata (tagline, highlight) -->
                        <div v-if="tenant.subscription.plan.metadata" class="mb-4">
                            <div v-if="tenant.subscription.plan.metadata.tagline" class="text-sm text-gray-600 mb-2">
                                <span class="text-lg">💡</span> {{ tenant.subscription.plan.metadata.tagline }}
                            </div>
                            <div v-if="tenant.subscription.plan.metadata.highlight" class="text-base font-medium text-gray-900">
                                {{ tenant.subscription.plan.metadata.highlight }}
                            </div>
                        </div>

                        <!-- Subscription Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-200">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Billing Period</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ tenant.subscription.plan.billing_period === 'monthly' ? 'Monthly' : 'Yearly' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Subscription Status</dt>
                                <dd class="mt-1">
                                    <span
                                        :class="{
                                            'bg-green-100 text-green-800': tenant.subscription?.status === 'active',
                                            'bg-yellow-100 text-yellow-800': tenant.subscription?.status === 'trial',
                                            'bg-red-100 text-red-800': tenant.subscription?.status === 'suspended',
                                        }"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ tenant.subscription?.status || 'N/A' }}
                                    </span>
                                </dd>
                            </div>
                            <div v-if="tenant.subscription.trial_ends_at">
                                <dt class="text-sm font-medium text-gray-500">Trial Ends At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(tenant.subscription.trial_ends_at).toLocaleDateString('sr-RS', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}
                                </dd>
                            </div>
                            <div v-if="tenant.subscription.starts_at">
                                <dt class="text-sm font-medium text-gray-500">Started At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(tenant.subscription.starts_at).toLocaleDateString('sr-RS', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}
                                </dd>
                            </div>
                            <div v-if="tenant.subscription.ends_at">
                                <dt class="text-sm font-medium text-gray-500">Ends At</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(tenant.subscription.ends_at).toLocaleDateString('sr-RS', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric' 
                                    }) }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <!-- No Plan Assigned Card -->
                    <div v-else 
                         class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center bg-gray-50">
                        <div class="mb-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Plan Assigned</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            This tenant doesn't have an active subscription plan. Assign a plan to enable full functionality.
                        </p>
                        <button
                            @click="showAssignPlanModal = true"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition-colors"
                        >
                            Assign Plan Now
                        </button>
                    </div>
                </div>

                <!-- Tenant Details Section -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tenant Details</h2>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': tenant.status === 'active',
                                        'bg-red-100 text-red-800': tenant.status === 'suspended',
                                        'bg-gray-100 text-gray-800': tenant.status === 'deleted',
                                    }"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ tenant.status }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.email || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.phone || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.country || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.timezone || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Currency</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.currency || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Slug</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ tenant.slug || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ tenant.created_at ? new Date(tenant.created_at).toLocaleString() : 'N/A' }}
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
import { Link, router } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const suspendTenant = () => {
    if (confirm('Are you sure you want to suspend this tenant?')) {
        router.post(route('tenants.suspend', props.tenant.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const activateTenant = () => {
    if (confirm('Are you sure you want to activate this tenant?')) {
        router.post(route('tenants.activate', props.tenant.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const impersonateTenant = () => {
    if (confirm('Da li ste sigurni da želite da se ulogujete kao Klijent (Impersonate)?')) {
        router.post(route('tenants.impersonate', props.tenant.id), {}, {
            preserveScroll: true,
        });
    }
};

// Helper functions
const formatPrice = (price) => {
    if (!price && price !== 0) return '0';
    return parseFloat(price).toFixed(2);
};

const getBillingPeriodLabel = (period) => {
    if (!period) return 'month';
    return period === 'monthly' ? 'month' : 'year';
};
</script>

