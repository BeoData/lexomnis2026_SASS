<template>
    <PublicLayout>
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Try LexOmnis for free</h1>
                <p class="text-lg text-gray-600">Start a 7-day free trial of LexOmnis.</p>
            </div>

            <div class="max-w-4xl mx-auto">
                <div class="bg-white shadow-lg rounded-lg p-8">
                    <form @submit.prevent="submit">
                        <!-- Personal Information Section -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Lični Podaci</h2>
                            <div class="grid grid-cols-1 gap-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                                            Ime *
                                        </label>
                                        <input
                                            id="first_name"
                                            v-model="form.first_name"
                                            type="text"
                                            required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Unesite vaše ime"
                                        />
                                        <div v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                                            {{ errors.first_name }}
                                        </div>
                                    </div>
                                    <div>
                                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                                            Prezime *
                                        </label>
                                        <input
                                            id="last_name"
                                            v-model="form.last_name"
                                            type="text"
                                            required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Unesite vaše prezime"
                                        />
                                        <div v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                                            {{ errors.last_name }}
                                        </div>
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
                                        autocomplete="username"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="vas@email.com"
                                    />
                                    <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">
                                            Lozinka *
                                        </label>
                                        <input
                                            id="password"
                                            v-model="form.password"
                                            type="password"
                                            required
                                            minlength="8"
                                            autocomplete="new-password"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        />
                                        <p class="mt-1 text-xs text-gray-500">Minimum 8 karaktera</p>
                                        <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                                            {{ errors.password }}
                                        </div>
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                            Potvrdite Lozinku *
                                        </label>
                                        <input
                                            id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            type="password"
                                            required
                                            minlength="8"
                                            autocomplete="new-password"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        Telefon
                                    </label>
                                    <input
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="+381..."
                                    />
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div>
                                        <label for="country" class="block text-sm font-medium text-gray-700">
                                            Država
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
                                            Vremenska Zona
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
                                            Valuta
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
                            </div>
                        </div>

                        <!-- Subscription Plan Section -->
                        <div class="border-t pt-8">
                            <h2 class="text-2xl font-semibold text-gray-900 mb-6">Izaberite Plan</h2>
                            
                            <!-- Plan Selection Type -->
                            <div class="mb-6">
                                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                           :class="subscriptionType === 'trial' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input
                                            type="radio"
                                            v-model="subscriptionType"
                                            value="trial"
                                            class="mr-3"
                                        />
                                        <div>
                                            <span class="font-medium text-gray-900">Free Trial (7 dana)</span>
                                            <p class="text-sm text-gray-600">Probajte besplatno 7 dana</p>
                                        </div>
                                    </label>
                                    <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                           :class="subscriptionType === 'paid' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'">
                                        <input
                                            type="radio"
                                            v-model="subscriptionType"
                                            value="paid"
                                            class="mr-3"
                                        />
                                        <div>
                                            <span class="font-medium text-gray-900">Izaberite Plan</span>
                                            <p class="text-sm text-gray-600">Odmah počnite sa plaćenim planom</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Plan Selection -->
                            <div>
                                <div class="mb-4 text-sm text-gray-600">
                                    {{ subscriptionType === 'trial'
                                        ? 'Možete ostaviti podrazumevani besplatni trial. Planovi će se prikazati samo kada izaberete plaćenu opciju.'
                                        : 'Odaberite plan koji želite da aktivirate odmah.'
                                    }}
                                </div>

                                <template v-if="subscriptionType === 'paid'">
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
                                            Mesečno
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
                                            Godišnje
                                        </button>
                                    </div>

                                    <!-- Plans Grid -->
                                    <div v-if="loadingPlans" class="text-center py-8">
                                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                    </div>
                                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                                        Nema dostupnih planova
                                    </div>
                                    <div v-if="!selectedPlan" class="mt-4 text-sm text-red-600">
                                        Molimo izaberite plan
                                    </div>

                                    <div class="mt-8 border-t pt-6">
                                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Način plaćanja</h3>
                                        <div class="grid grid-cols-1 gap-4">
                                            <label class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors"
                                                   :class="form.payment_method === 'stripe'
                                                       ? 'border-blue-500 bg-blue-50'
                                                       : 'border-gray-200 hover:border-gray-300'">
                                                <input
                                                    type="radio"
                                                    v-model="form.payment_method"
                                                    value="stripe"
                                                    class="mr-3"
                                                />
                                                <div>
                                                    <span class="font-medium text-gray-900">Stripe</span>
                                                    <p class="text-sm text-gray-600">Plaćanje karticom putem Stripe-a</p>
                                                </div>
                                            </label>
                                            <label v-if="paypalEnabled" class="flex items-center p-4 border rounded-lg cursor-pointer transition-colors"
                                                   :class="form.payment_method === 'paypal'
                                                       ? 'border-blue-500 bg-blue-50'
                                                       : 'border-gray-200 hover:border-gray-300'">
                                                <input
                                                    type="radio"
                                                    v-model="form.payment_method"
                                                    value="paypal"
                                                    class="mr-3"
                                                />
                                                <div>
                                                    <span class="font-medium text-gray-900">PayPal</span>
                                                    <p class="text-sm text-gray-600">Plaćanje preko PayPal naloga</p>
                                                </div>
                                            </label>
                                            <div v-else class="text-sm text-gray-500">
                                                PayPal nije dostupan trenutno. Koristite Stripe ili konfigurirajte PayPal u .env.
                                            </div>
                                        </div>
                                        <p class="mt-4 text-sm text-gray-500">
                                            Bićete preusmereni na bezbednu stranu za plaćanje nakon potvrde registracije.
                                        </p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div v-if="errors.error" class="mt-6 p-4 bg-red-50 border border-red-200 rounded-md">
                            <p class="text-sm text-red-800">{{ errors.error }}</p>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end space-x-3">
                            <button
                                type="submit"
                                :disabled="form.processing || (subscriptionType === 'paid' && !selectedPlan)"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                            >
                                <span v-if="form.processing">Registruje se...</span>
                                <span v-else>Registrujte Se</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Pages/Layouts/PublicLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import PlanCard from '@/Components/PlanCard.vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const props = defineProps({
    errors: {
        type: Object,
        default: () => ({}),
    },
    groupedPlans: {
        type: Array,
        default: () => [],
    },
    initial_registration_type: {
        type: String,
        default: 'trial',
    },
    initial_billing_period: {
        type: String,
        default: 'monthly',
    },
    initial_plan_id: {
        type: [Number, String],
        default: null,
    },
    paypal_enabled: {
        type: Boolean,
        default: false,
    },
});

const subscriptionType = ref(props.initial_registration_type || 'trial');
const selectedBillingPeriod = ref(
    ['monthly', 'yearly'].includes(props.initial_billing_period)
        ? props.initial_billing_period
        : 'monthly'
);
const selectedPlan = ref(null);
const groupedPlans = ref(props.groupedPlans || []);
const loadingPlans = ref(false);
const paypalEnabled = ref(props.paypal_enabled ?? false);

const form = useForm({
    first_name: '',
    last_name: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    country: 'RS',
    timezone: 'Europe/Belgrade',
    currency: 'RSD',
    registration_type: 'trial',
    plan_id: null,
    billing_period: null,
    payment_method: 'stripe',
});

const updateSelectedPlan = () => {
    if (!selectedPlan.value) {
        return;
    }

    const plan = selectedBillingPeriod.value === 'monthly'
        ? selectedPlan.value.monthly
        : selectedPlan.value.yearly;

    if (plan) {
        form.plan_id = plan.id;
        form.billing_period = plan.billing_period;
    }
};

const selectPlan = (planGroup) => {
    selectedPlan.value = planGroup;
    updateSelectedPlan();
};

watch(selectedBillingPeriod, () => {
    updateSelectedPlan();
});

watch(subscriptionType, (newValue) => {
    if (newValue === 'trial') {
        selectedPlan.value = null;
        form.plan_id = null;
        form.billing_period = null;
    } else {
        updateSelectedPlan();
    }
});

onMounted(() => {
    if (props.initial_plan_id) {
        const planId = Number(props.initial_plan_id);
        const planGroup = groupedPlans.value.find((group) => {
            return (group.monthly?.id === planId) || (group.yearly?.id === planId);
        });

        if (planGroup) {
            selectedPlan.value = planGroup;
            const plan = selectedBillingPeriod.value === 'monthly'
                ? planGroup.monthly
                : planGroup.yearly;

            if (plan && plan.id === planId) {
                form.plan_id = plan.id;
                form.billing_period = plan.billing_period;
            } else {
                const fallbackPlan = planGroup.monthly || planGroup.yearly;
                if (fallbackPlan) {
                    form.plan_id = fallbackPlan.id;
                    form.billing_period = fallbackPlan.billing_period;
                    selectedBillingPeriod.value = fallbackPlan.billing_period;
                }
            }
        }
    }
});

const submit = () => {
    form.registration_type = subscriptionType.value;
    
    if (subscriptionType.value === 'paid' && !selectedPlan.value) {
        return;
    }

    // Combine first_name and last_name into name for backend
    form.name = `${form.first_name} ${form.last_name}`.trim();

    if (subscriptionType.value === 'trial' && !selectedPlan.value) {
        form.plan_id = null;
        form.billing_period = null;
    }

    const targetRoute = route('tenant.register.store');
    
    form.post(targetRoute, {
        preserveScroll: true,
        onError: () => {
            // Errors are handled by Inertia
        },
    });
};
</script>
