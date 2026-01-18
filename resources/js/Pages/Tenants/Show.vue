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
                        <button
                            @click="showAssignPlanModal = true"
                            :class="{
                                'bg-blue-600 hover:bg-blue-700': !tenant.subscription,
                                'bg-gray-600 hover:bg-gray-700': tenant.subscription
                            }"
                            class="text-white font-medium py-2 px-4 rounded text-sm transition-colors"
                        >
                            <span v-if="!tenant.subscription">Assign Plan</span>
                            <span v-else>Change Plan</span>
                        </button>
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
                                        'bg-yellow-100 text-yellow-800': tenant.status === 'trial',
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

                <!-- Assign Plan Modal -->
                <div v-if="showAssignPlanModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click.self="showAssignPlanModal = false">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                        <div class="mt-3">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Assign Plan</h3>
                            <div v-if="loadingPlans" class="text-center py-4">
                                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                            <div v-else>
                                <!-- Billing Period Tabs -->
                                <div class="flex space-x-2 mb-6 border-b">
                                    <button
                                        @click="selectedBillingPeriod = 'monthly'"
                                        :class="{
                                            'border-b-2 border-blue-600 text-blue-600': selectedBillingPeriod === 'monthly',
                                            'text-gray-500': selectedBillingPeriod !== 'monthly'
                                        }"
                                        class="px-4 py-2 font-medium"
                                    >
                                        Monthly
                                    </button>
                                    <button
                                        @click="selectedBillingPeriod = 'yearly'"
                                        :class="{
                                            'border-b-2 border-blue-600 text-blue-600': selectedBillingPeriod === 'yearly',
                                            'text-gray-500': selectedBillingPeriod !== 'yearly'
                                        }"
                                        class="px-4 py-2 font-medium"
                                    >
                                        Annually
                                    </button>
                                </div>

                                <!-- Plans Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                                    <div
                                        v-for="planGroup in groupedPlans"
                                        :key="planGroup.plan_key"
                                        @click="selectPlan(planGroup)"
                                        :class="{
                                            'ring-2 ring-blue-600': selectedPlan?.plan_key === planGroup.plan_key,
                                            'hover:shadow-lg': true
                                        }"
                                        class="border rounded-lg p-4 cursor-pointer transition-shadow"
                                    >
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-semibold text-lg">{{ planGroup.name }}</h4>
                                            <span v-if="planGroup.metadata?.popular" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Popular</span>
                                        </div>
                                        <div class="text-2xl font-bold mb-2">
                                            ${{ selectedBillingPeriod === 'monthly' ? planGroup.monthly?.price : planGroup.yearly?.price }}
                                            <span class="text-sm font-normal text-gray-500">USD</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mb-2">
                                            {{ planGroup.metadata?.tagline || '' }}
                                        </div>
                                        <div class="text-sm font-medium mb-2">
                                            {{ planGroup.metadata?.highlight || '' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end space-x-3">
                                    <button
                                        @click="showAssignPlanModal = false"
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        @click="assignPlan"
                                        :disabled="!selectedPlan || assigningPlan"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50"
                                    >
                                        <span v-if="assigningPlan">Assigning...</span>
                                        <span v-else>Assign Plan</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const showAssignPlanModal = ref(false);
const loadingPlans = ref(false);
const groupedPlans = ref([]);
const selectedBillingPeriod = ref('monthly');
const selectedPlan = ref(null);
const assigningPlan = ref(false);

const loadPlans = async () => {
    loadingPlans.value = true;
    try {
        // Use TenantAppApiService endpoint
        const baseUrl = window.location.origin.replace(':8001', ':8000'); // Tenant app URL
        const apiToken = document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '';
        
        const response = await axios.get(`${baseUrl}/api/admin/plans`, {
            params: {
                grouped: true,
                is_active: true,
            },
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Accept': 'application/json',
            },
        });
        groupedPlans.value = response.data;
    } catch (error) {
        console.error('Failed to load plans:', error);
        alert('Failed to load plans. Please try again.');
    } finally {
        loadingPlans.value = false;
    }
};

const selectPlan = (planGroup) => {
    selectedPlan.value = planGroup;
};

const assignPlan = async () => {
    if (!selectedPlan.value) return;

    const plan = selectedBillingPeriod.value === 'monthly' 
        ? selectedPlan.value.monthly 
        : selectedPlan.value.yearly;

    if (!plan) {
        alert('Selected plan is not available for this billing period.');
        return;
    }

    assigningPlan.value = true;
    try {
        // Use TenantAppApiService endpoint
        const baseUrl = window.location.origin.replace(':8001', ':8000'); // Tenant app URL
        const apiToken = document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '';
        
        const response = await axios.post(`${baseUrl}/api/admin/tenants/${props.tenant.id}/assign-plan`, {
            plan_id: plan.id,
            billing_period: selectedBillingPeriod.value,
        }, {
            headers: {
                'Authorization': `Bearer ${apiToken}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        if (response.data.success || response.data.message) {
            showAssignPlanModal.value = false;
            router.reload();
        } else {
            alert('Failed to assign plan: ' + (response.data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Failed to assign plan:', error);
        alert('Failed to assign plan. Please try again.');
    } finally {
        assigningPlan.value = false;
    }
};

// Load plans when modal opens
watch(showAssignPlanModal, (newVal) => {
    if (newVal) {
        loadPlans();
    }
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

