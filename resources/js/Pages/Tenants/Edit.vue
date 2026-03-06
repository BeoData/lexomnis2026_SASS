<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6 flex justify-between items-center">
                    <Link
                        :href="route('tenants.show', tenant.id)"
                        class="text-blue-600 hover:text-blue-800 text-sm flex items-center"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Tenant
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Tenant: {{ tenant.name }}</h1>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <form @submit.prevent="submit">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</div>
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Status
                                    </label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    >
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspended</option>
                                        <option value="deleted">Deleted</option>
                                    </select>
                                    <div v-if="form.errors.status" class="mt-1 text-xs text-red-600">{{ form.errors.status }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Plan Selection Section - No Modals! -->
                        <div class="p-6 bg-gray-50 border-b border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-semibold text-gray-800">Assign / Change Plan</h2>
                                <div class="flex bg-white rounded-lg p-1 shadow-sm border border-gray-200">
                                    <button
                                        type="button"
                                        @click="selectedBillingPeriod = 'monthly'"
                                        :class="selectedBillingPeriod === 'monthly' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                        class="px-4 py-1.5 text-xs font-semibold rounded-md transition-all"
                                    >
                                        Monthly
                                    </button>
                                    <button
                                        type="button"
                                        @click="selectedBillingPeriod = 'yearly'"
                                        :class="selectedBillingPeriod === 'yearly' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                        class="px-4 py-1.5 text-xs font-semibold rounded-md transition-all"
                                    >
                                        Annually
                                    </button>
                                </div>
                            </div>

                            <div v-if="loadingPlans" class="flex justify-center py-12">
                                <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                            </div>

                            <div v-else-if="groupedPlans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div
                                    v-for="planGroup in groupedPlans"
                                    :key="planGroup.plan_key"
                                    @click="selectPlan(planGroup)"
                                    :class="getPlanCardClasses(planGroup)"
                                    class="relative border-2 rounded-xl p-5 cursor-pointer transition-all duration-200"
                                >
                                    <!-- Selected Indicator -->
                                    <div v-if="isSelectedPlan(planGroup)" class="absolute -top-2 -right-2 bg-blue-600 text-white rounded-full p-1 shadow-md">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>

                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-bold text-lg text-gray-900">{{ planGroup.name }}</h4>
                                        <span v-if="planGroup.metadata?.popular" class="bg-blue-100 text-blue-800 text-[10px] uppercase tracking-wider font-bold px-2 py-1 rounded">Popular</span>
                                    </div>
                                    
                                    <div class="flex items-baseline mb-3">
                                        <span class="text-3xl font-extrabold text-blue-600">
                                            ${{ selectedBillingPeriod === 'monthly' ? planGroup.monthly?.price : planGroup.yearly?.price }}
                                        </span>
                                        <span class="text-sm text-gray-500 ml-1">USD / {{ selectedBillingPeriod === 'monthly' ? 'mo' : 'yr' }}</span>
                                    </div>

                                    <p class="text-xs text-gray-600 mb-4 line-clamp-2">
                                        {{ planGroup.metadata?.tagline || 'Standard features for legal teams.' }}
                                    </p>

                                    <div class="space-y-2">
                                        <div v-if="planGroup.metadata?.highlight" class="text-xs font-semibold text-gray-800 bg-gray-100 p-2 rounded flex items-center">
                                            <span class="mr-1.5">⚡</span> {{ planGroup.metadata.highlight }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="text-center py-12 bg-white rounded-lg border-2 border-dashed">
                                <p class="text-red-500 font-medium">Failed to load plans. Please check API connection.</p>
                                <button type="button" @click="fetchPlans" class="mt-4 text-blue-600 hover:underline text-sm font-semibold">Try again</button>
                            </div>
                            <div v-if="form.errors.plan_id" class="mt-4 text-sm text-red-600 font-medium">{{ form.errors.plan_id }}</div>
                        </div>

                        <div class="p-6 bg-white flex justify-end space-x-4">
                            <Link
                                :href="route('tenants.show', tenant.id)"
                                class="inline-flex items-center px-6 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center px-8 py-2 border border-transparent text-sm font-semibold rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all disabled:opacity-50"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Updating...' : 'Save Changes' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const groupedPlans = ref([]);
const loadingPlans = ref(false);
const selectedBillingPeriod = ref(props.tenant.subscription?.plan?.billing_period || 'monthly');

const form = useForm({
    name: props.tenant.name || '',
    email: props.tenant.email || '',
    status: props.tenant.status || 'active',
    plan_id: props.tenant.subscription?.plan?.id || null, // Pre-select current plan if exists
    billing_period: props.tenant.subscription?.plan?.billing_period || 'monthly',
});

const fetchPlans = async () => {
    loadingPlans.value = true;
    try {
        const response = await axios.get('/plans/list');
        groupedPlans.value = response.data;
    } catch (error) {
        console.error('Failed to load plans:', error);
    } finally {
        loadingPlans.value = false;
    }
};

const selectPlan = (planGroup) => {
    const plan = selectedBillingPeriod.value === 'monthly' ? planGroup.monthly : planGroup.yearly;
    if (plan) {
        form.plan_id = plan.id;
        form.billing_period = selectedBillingPeriod.value;
    }
};

const isSelectedPlan = (planGroup) => {
    const plan = selectedBillingPeriod.value === 'monthly' ? planGroup.monthly : planGroup.yearly;
    return form.plan_id === plan?.id;
};

const getPlanCardClasses = (planGroup) => {
    const isSelected = isSelectedPlan(planGroup);
    return isSelected 
        ? 'border-blue-600 bg-blue-50 shadow-md ring-1 ring-blue-600' 
        : 'border-gray-200 bg-white hover:border-blue-300';
};

const submit = () => {
    form.put(route('tenants.update', props.tenant.id));
};

onMounted(() => {
    fetchPlans();
});
</script>

