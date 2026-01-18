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

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Tenant</h1>

                <div class="bg-white shadow rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Name *
                                </label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                                    {{ errors.name }}
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Email *
                                </label>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                    {{ errors.email }}
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Password (optional - will be auto-generated if empty)
                                </label>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    minlength="8"
                                    placeholder="Leave empty for auto-generated password"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                                <p class="mt-1 text-xs text-gray-500">Minimum 8 characters (or leave empty for auto-generation)</p>
                                <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                    {{ errors.password }}
                                </div>
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Phone
                                </label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                />
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label for="country" class="block text-sm font-medium text-gray-700">
                                        Country
                                    </label>
                                    <input
                                        id="country"
                                        v-model="form.country"
                                        type="text"
                                        value="RS"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">
                                        Timezone
                                    </label>
                                    <input
                                        id="timezone"
                                        v-model="form.timezone"
                                        type="text"
                                        value="Europe/Belgrade"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                                <div>
                                    <label for="currency" class="block text-sm font-medium text-gray-700">
                                        Currency
                                    </label>
                                    <input
                                        id="currency"
                                        v-model="form.currency"
                                        type="text"
                                        value="RSD"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <!-- Subscription Plan Section -->
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Plan</h3>
                                
                                <!-- Plan Selection Type -->
                                <div class="mb-6">
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="subscriptionType"
                                                value="trial"
                                                class="mr-2"
                                            />
                                            <span>Free Trial (7 days)</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input
                                                type="radio"
                                                v-model="subscriptionType"
                                                value="plan"
                                                class="mr-2"
                                            />
                                            <span>Select Plan</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Plan Selection (shown when "Select Plan" is chosen) -->
                                <div v-if="subscriptionType === 'plan'">
                                    <!-- Billing Period Tabs -->
                                    <div class="flex space-x-2 mb-6 border-b">
                                        <button
                                            type="button"
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
                                            type="button"
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
                                    <div v-if="loadingPlans" class="text-center py-8">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    </div>
                                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <PlanCard
                                            v-for="planGroup in groupedPlans"
                                            :key="planGroup.plan_key"
                                            :plan-group="planGroup"
                                            :selected-billing-period="selectedBillingPeriod"
                                            :is-selected="selectedPlan?.plan_key === planGroup.plan_key"
                                            @select="selectPlan"
                                        />
                                    </div>
                                    <div v-if="!loadingPlans && groupedPlans.length === 0" class="text-center py-8 text-gray-500">
                                        No plans available
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <Link
                                :href="route('tenants.index')"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Tenant</span>
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
import { ref } from 'vue';
import { useRoute } from '@/composables/useRoute';
import PlanCard from '@/Components/PlanCard.vue';

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
    groupedPlans: {
        type: Array,
        default: () => [],
    },
});

const { route } = useRoute();

const subscriptionType = ref('trial');
const selectedBillingPeriod = ref('monthly');
const selectedPlan = ref(null);
const groupedPlans = ref(props.groupedPlans || []);
const loadingPlans = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    phone: '',
    country: 'RS',
    timezone: 'Europe/Belgrade',
    currency: 'RSD',
    plan_id: null,
    billing_period: null,
    trial_days: 7,
});

// Plans are passed as props from the controller, no need to load them

const selectPlan = (planGroup) => {
    selectedPlan.value = planGroup;
    const plan = selectedBillingPeriod.value === 'monthly' 
        ? planGroup.monthly 
        : planGroup.yearly;
    
    if (plan) {
        form.plan_id = plan.id;
        form.billing_period = selectedBillingPeriod.value;
    }
};

const submit = () => {
    if (form.processing) {
        return; // Prevent double submission
    }

    // Validate required fields
    if (!form.name || !form.email) {
        alert('Please fill in all required fields (Name, Email).');
        return;
    }

    // Validate password length if provided
    if (form.password && form.password.length > 0 && form.password.length < 8) {
        alert('Password must be at least 8 characters long.');
        return;
    }

    // Validate plan selection if "Select Plan" is chosen
    if (subscriptionType.value === 'plan' && !form.plan_id) {
        alert('Please select a plan or choose Free Trial.');
        return;
    }

    // Clear plan_id if free trial is selected
    if (subscriptionType.value === 'trial') {
        form.plan_id = null;
        form.billing_period = null;
    }
    
    // Log form data for debugging
    console.log('Submitting form:', {
        name: form.name,
        email: form.email,
        password: form.password ? '***' : 'MISSING',
        plan_id: form.plan_id,
        billing_period: form.billing_period,
    });
    
    form.post(route('tenants.store'), {
        onError: (errors) => {
            console.error('Form errors:', errors);
            if (errors.error) {
                alert('Error: ' + errors.error);
            }
        },
    });
};
</script>

