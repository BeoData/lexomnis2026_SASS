<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link :href="route('home')" class="text-2xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                            {{ appName }}
                        </Link>
                    </div>

                    <!-- Desktop Navigation -->
                    <nav class="hidden md:flex items-center space-x-6">
                        <Link
                            :href="route('pricing')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Pricing
                        </Link>
                        <Link
                            :href="route('home') + '#features'"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Features
                        </Link>
                        <Link
                            :href="route('login')"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                        >
                            Log In
                        </Link>
                        <Link
                            :href="route('tenant.register') + '?cta=signup-header'"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-md hover:shadow-lg"
                        >
                            Start Free Trial
                        </Link>
                    </nav>

                    <!-- Mobile Menu Button -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Mobile Navigation -->
                <div v-if="mobileMenuOpen" class="md:hidden pb-4 border-t border-gray-200 mt-4">
                    <nav class="flex flex-col space-y-2">
                        <Link
                            :href="route('pricing')"
                            @click="mobileMenuOpen = false"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Pricing
                        </Link>
                        <Link
                            :href="route('home') + '#features'"
                            @click="mobileMenuOpen = false"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Features
                        </Link>
                        <Link
                            :href="route('login')"
                            @click="mobileMenuOpen = false"
                            class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Log In
                        </Link>
                        <Link
                            :href="route('tenant.register') + '?cta=signup-header'"
                            @click="mobileMenuOpen = false"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold text-center"
                        >
                            Start Free Trial
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
        <footer class="bg-gray-900 text-gray-300 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">About</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link :href="route('home')" class="hover:text-white transition-colors">
                                    Home
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('home') + '#features'" class="hover:text-white transition-colors">
                                    Features
                                </Link>
                            </li>
                            <li>
                                <Link :href="route('pricing')" class="hover:text-white transition-colors">
                                    Pricing
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Product -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Product</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Time Tracking</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Billing</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Document Management</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Client Portal</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Resources -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Resources</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Documentation</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Support</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Blog</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Legal -->
                    <div>
                        <h3 class="text-white font-semibold mb-4">Legal</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                            </li>
                            <li>
                                <a href="#" class="hover:text-white transition-colors">Security</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm">
                    <p>&copy; {{ currentYear }} {{ appName }}. Sva prava zadržana.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useRoute } from '@/composables/useRoute';

const { route } = useRoute();
const mobileMenuOpen = ref(false);

const appName = computed(() => {
    return document.querySelector('meta[name="app-name"]')?.getAttribute('content') || 'LexOmnis';
});

const currentYear = computed(() => {
    return new Date().getFullYear();
});
</script>
