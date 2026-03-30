<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Ultra-Premium Glassmorphism Navigation -->
        <nav class="sticky top-0 z-50 bg-blue-700 border-b border-blue-800 shadow-2xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20">
                    
                    <!-- Left Side: Branding & Navigation -->
                    <div class="flex items-center gap-10">
                        <!-- Advanced Brand Logo -->
                        <Link :href="route('dashboard')" class="flex items-center gap-3 group transition-all duration-300">
                            <div class="relative">
                                <div class="absolute -inset-1 bg-white/20 rounded-xl blur opacity-0 group-hover:opacity-100 transition duration-300"></div>
                                <div class="relative bg-white/10 p-2 rounded-xl border border-white/20 flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-white tracking-wider leading-none uppercase">LEXOMNIS</span>
                                <span class="text-[10px] font-bold text-blue-200 tracking-[0.2em] mt-1">SUPER ADMIN</span>
                            </div>
                        </Link>

                        <!-- Nav Links -->
                        <div class="hidden lg:flex items-center gap-1">
                            <!-- Dashboard -->
                            <Link
                                :href="route('dashboard')"
                                :class="isCurrentRoute('dashboard') ? 'bg-white/20 text-white border-white/30' : 'text-blue-100 hover:text-white hover:bg-white/10 border-transparent'"
                                class="px-4 py-2 rounded-lg text-sm font-bold border-2 transition-all duration-300 flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                Dashboard
                            </Link>

                            <!-- Intelligent Dropdowns -->
                            <template v-for="menu in [
                                { name: 'Management', routes: ['tenants.*', 'users.*'], items: [{label: 'Tenants', route: 'tenants.index', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'}, {label: 'Users', route: 'users.index', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'}] },
                                { name: 'Billing', routes: ['plans.*', 'subscriptions.*'], items: [{label: 'Plans', route: 'plans.index', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'}, {label: 'Subscriptions', route: 'subscriptions.index', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'}] },
                                { name: 'Monitoring', routes: ['system.*', 'audit-logs.*'], items: [{label: 'System Health', route: 'system.index', icon: 'M13 10V3L4 14h7v7l9-11h-7z'}, {label: 'Audit Logs', route: 'audit-logs.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'}] },
                                { name: 'Config', routes: ['settings.*', 'feature-flags.*'], items: [{label: 'Settings', route: 'settings.index', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'}, {label: 'Feature Flags', route: 'feature-flags.index', icon: 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'}] }
                            ]" :key="menu.name">
                                <div class="relative group">
                                    <button
                                        :class="menu.routes.some(r => isCurrentRoute(r)) ? 'text-white bg-white/20' : 'text-blue-100 hover:text-white hover:bg-white/10'"
                                        class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-bold transition-all duration-300"
                                    >
                                        {{ menu.name }}
                                        <svg class="w-4 h-4 opacity-50 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <!-- Premium Dropdown Menu -->
                                    <div class="absolute left-0 mt-2 w-64 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 transition-all duration-300 z-[100]">
                                        <div class="p-2 bg-white rounded-2xl shadow-2xl border border-slate-200">
                                            <Link v-for="item in menu.items" :key="item.label" :href="route(item.route)" 
                                                class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group/item"
                                            >
                                                <div class="p-2 bg-slate-100 rounded-lg group-hover/item:bg-blue-100 group-hover/item:text-blue-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" /></svg>
                                                </div>
                                                <span class="text-sm font-semibold">{{ item.label }}</span>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Right Side: Profile & Exit -->
                    <div class="flex items-center gap-6">
                        <div class="hidden md:flex flex-col items-end">
                            <span class="text-sm font-bold text-white tracking-wide">
                                {{ $page.props.auth?.user?.name || $page.props.auth?.user?.email || 'System Root' }}
                            </span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="w-2 h-2 rounded-full bg-blue-300 animate-pulse"></span>
                                <span class="text-[10px] font-black text-blue-200 uppercase tracking-tighter">Verified Superadmin</span>
                            </div>
                        </div>

                        <!-- Profile Avatar -->
                        <div class="relative group">
                            <div class="h-12 w-12 rounded-2xl bg-white/20 border border-white/30 flex items-center justify-center shadow-xl group-hover:scale-105 transition-transform duration-300 cursor-pointer">
                                <span class="text-lg font-black text-white">
                                    {{ ($page.props.auth?.user?.name || $page.props.auth?.user?.email || 'SA').substring(0,1).toUpperCase() }}
                                </span>
                            </div>
                        </div>

                        <!-- Global Logout Button -->
                        <form @submit.prevent="logout" method="post">
                            <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white text-white hover:text-blue-700 border border-white/20 rounded-xl text-sm font-black transition-all duration-300 shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                <span>Logout</span>
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
