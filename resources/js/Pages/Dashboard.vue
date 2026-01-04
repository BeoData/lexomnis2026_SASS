<template>
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        Super Admin Dashboard
                    </h1>
                    <p class="text-gray-600 mb-6">
                        Welcome to LexOmnis Super Admin Panel
                    </p>
                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h2 class="text-xl font-semibold mb-2">Tenant Management</h2>
                            <p class="text-gray-600">Manage all tenants (firms) in the system</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h2 class="text-xl font-semibold mb-2">User Management</h2>
                            <p class="text-gray-600">View and manage all users across tenants</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h2 class="text-xl font-semibold mb-2">System Monitoring</h2>
                            <p class="text-gray-600">Monitor system health and performance</p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <form @submit.prevent="logout" method="post">
                            <button
                                type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout');
};
</script>

