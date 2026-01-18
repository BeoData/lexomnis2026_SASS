<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <h1 class="text-2xl font-bold text-gray-900">{{ appName }}</h1>
                    </div>
                    <nav class="flex space-x-4">
                        <Link
                            :href="route('pricing')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Pricing
                        </Link>
                        <Link
                            :href="route('login')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Login
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-gray-600 text-sm">
                    <p>&copy; {{ currentYear }} {{ appName }}. Sva prava zadržana.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();

const appName = computed(() => {
    return document.querySelector('meta[name="app-name"]')?.getAttribute('content') || 'LexOmnis';
});

const currentYear = computed(() => {
    return new Date().getFullYear();
});
</script>
