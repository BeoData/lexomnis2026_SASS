<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Cancelled</h1>
                <p class="text-gray-600 mb-6">Your payment was cancelled. No charges were made.</p>
                
                <div class="space-y-4">
                    <Link
                        :href="route('pricing')"
                        class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition-colors"
                    >
                        Back to Pricing
                    </Link>
                    <Link
                        :href="route('dashboard')"
                        class="block text-blue-600 hover:text-blue-800 font-medium"
                    >
                        Go to Dashboard
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>
