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
                
                <div v-if="$page.props.flash?.message" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-md">
                    <p class="text-green-800">{{ $page.props.flash.message }}</p>
                </div>
                
                <p class="text-lg text-gray-600 mb-8">
                    Vaša registracija je uspešno završena!
                </p>
                
                <div class="space-y-4">
                    <Link
                        :href="route('login')"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition-colors"
                    >
                        Prijavite Se
                    </Link>
                    <div>
                        <Link
                            :href="route('pricing')"
                            class="text-blue-600 hover:text-blue-800 text-sm"
                        >
                            Vratite se na Pricing
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>

<script setup>
import PublicLayout from '@/Pages/Layouts/PublicLayout.vue';
import { Link } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();
</script>
