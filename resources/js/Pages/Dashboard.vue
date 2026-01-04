<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">
                    Super Admin Dashboard
                </h1>
                <p class="text-gray-600 mb-8">
                    Welcome to LexOmnis Super Admin Panel
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link
                        :href="route('tenants.index')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">Tenant Management</h2>
                        <p class="text-gray-600">Manage all tenants (firms) in the system</p>
                    </Link>
                    <Link
                        :href="route('users.index')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">User Management</h2>
                        <p class="text-gray-600">View and manage all users across tenants</p>
                    </Link>
                    <Link
                        :href="route('system.index')"
                        class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition-shadow"
                    >
                        <h2 class="text-xl font-semibold mb-2 text-gray-900">System Monitoring</h2>
                        <p class="text-gray-600">Monitor system health and performance</p>
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
</script>

