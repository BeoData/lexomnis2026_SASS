<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">LexOmnis Super Admin</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-1">
                            <!-- Dashboard -->
                            <Link
                                :href="route('dashboard')"
                                :class="isCurrentRoute('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                                class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium"
                                style="padding-top: 22px;"
                            >
                                Dashboard
                            </Link>

                            <!-- Management Dropdown -->
                            <div class="relative group">
                                <button
                                    :class="isCurrentRoute('tenants.*') || isCurrentRoute('users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium"
                                    style="padding-top: 22px;"
                                >
                                    Management
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <Link
                                            :href="route('tenants.index')"
                                            :class="isCurrentRoute('tenants.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Tenants
                                        </Link>
                                        <Link
                                            :href="route('users.index')"
                                            :class="isCurrentRoute('users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Users
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Dropdown -->
                            <div class="relative group">
                                <button
                                    :class="isCurrentRoute('plans.*') || isCurrentRoute('subscriptions.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium"
                                    style="padding-top: 22px;"
                                >
                                    Billing
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <Link
                                            :href="route('plans.index')"
                                            :class="isCurrentRoute('plans.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Plans
                                        </Link>
                                        <Link
                                            :href="route('subscriptions.index')"
                                            :class="isCurrentRoute('subscriptions.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Subscriptions
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Monitoring Dropdown -->
                            <div class="relative group">
                                <button
                                    :class="isCurrentRoute('system.*') || isCurrentRoute('audit-logs.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium"
                                    style="padding-top: 22px;"
                                >
                                    Monitoring
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <Link
                                            :href="route('system.index')"
                                            :class="isCurrentRoute('system.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            System Health
                                        </Link>
                                        <Link
                                            :href="route('audit-logs.index')"
                                            :class="isCurrentRoute('audit-logs.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Audit Logs
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuration Dropdown -->
                            <div class="relative group">
                                <button
                                    :class="isCurrentRoute('settings.*') || isCurrentRoute('feature-flags.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium"
                                    style="padding-top: 22px;"
                                >
                                    Configuration
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <Link
                                            :href="route('settings.index')"
                                            :class="isCurrentRoute('settings.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Settings
                                        </Link>
                                        <Link
                                            :href="route('feature-flags.index')"
                                            :class="isCurrentRoute('feature-flags.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700'"
                                            class="block px-4 py-2 text-sm hover:bg-gray-100"
                                        >
                                            Feature Flags
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 mr-4">{{ $page.props.auth.user?.name || $page.props.auth.user?.email }}</span>
                        <form @submit.prevent="logout" method="post">
                            <button
                                type="submit"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { useRoute } from '@/composables/useRoute';

const page = usePage();
const { route } = useRoute();

const isCurrentRoute = (pattern) => {
    const currentUrl = page.url;
    if (pattern.endsWith('.*')) {
        const base = pattern.replace('.*', '');
        return currentUrl.startsWith('/' + base);
    }
    if (pattern === 'dashboard') {
        return currentUrl === '/dashboard' || currentUrl === '/';
    }
    return currentUrl.startsWith('/' + pattern);
};

const logout = () => {
    router.post('/logout');
};
</script>
