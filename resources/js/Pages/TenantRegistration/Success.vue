<template>
    <PublicLayout>
        <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg p-8 text-center">
                <div class="mb-6">
                    <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-4">Uspešno!</h1>
                
                <div v-if="confirmationError" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-red-800">{{ confirmationError }}</p>
                </div>
                <div v-else-if="confirmationMessage || $page.props.flash?.message" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-green-800">{{ confirmationMessage || $page.props.flash.message }}</p>
                </div>
                
                <p class="text-lg text-gray-600 mb-8">
                    Vaša registracija je uspešno završena! Sada imate pristup svom radnom okruženju (Tenant) i portalu za klijente (SASS Profil).
                </p>
                
                <div class="flex flex-col gap-4 max-w-sm mx-auto">
                    <!-- Primarna akcija: Ulazak u samu aplikaciju (tenant app - dinamički URL) -->
                    <a
                        :href="tenantLoginUrl"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg shadow-lg transform transition-all hover:scale-105"
                    >
                        Uđite u Aplikaciju →
                    </a>

                    <div class="flex items-center justify-center my-2 text-gray-400">
                        <div class="border-t border-gray-200 flex-grow mr-3"></div>
                        <span class="text-xs uppercase font-semibold">ili</span>
                        <div class="border-t border-gray-200 flex-grow ml-3"></div>
                    </div>

                    <!-- Sekundarna akcija: Upravljanje profilom na SASS portalu -->
                    <Link
                        :href="route('login')"
                        class="inline-block bg-white hover:bg-gray-50 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors border border-gray-300"
                    >
                        Upravljaj Profilom i Računima
                    </Link>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Pages/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';
import { computed } from 'vue';

const { route } = useRoute();

const props = defineProps({
    tenantAppUrl: {
        type: String,
        default: '',
    },
    confirmation_message: {
        type: String,
        default: '',
    },
    confirmation_error: {
        type: String,
        default: '',
    },
});

const confirmationMessage = computed(() => props.confirmation_message);
const confirmationError = computed(() => props.confirmation_error);

// Build tenant login URL dynamically from backend-provided tenantAppUrl
const tenantLoginUrl = computed(() => {
    if (props.tenantAppUrl) {
        return props.tenantAppUrl + '/login';
    }
    // Fallback — should never happen if backend is configured correctly
    return '/login';
});
</script>
