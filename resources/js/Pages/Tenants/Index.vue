<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Tenants</h1>
                    <Link
                        :href="route('tenants.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Create Tenant
                    </Link>
                </div>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form @submit.prevent="filter" class="flex gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search tenants..."
                            class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                        />
                        <select
                            v-model="statusFilter"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="trial">Trial</option>
                        </select>
                        <button
                            type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Tenants Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="tenant in tenants" :key="tenant.id">
                            <Link
                                :href="route('tenants.show', tenant.id)"
                                class="block hover:bg-gray-50 px-4 py-4 sm:px-6"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span
                                                :class="{
                                                    'bg-green-100 text-green-800': tenant.status === 'active',
                                                    'bg-red-100 text-red-800': tenant.status === 'suspended',
                                                    'bg-yellow-100 text-yellow-800': tenant.status === 'trial',
                                                }"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                            >
                                                {{ tenant.status }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ tenant.name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ tenant.email || 'No email' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button
                                            v-if="tenant.status === 'active'"
                                            @click.stop.prevent="suspendTenant(tenant.id)"
                                            class="text-yellow-600 hover:text-yellow-900 text-sm"
                                        >
                                            Suspend
                                        </button>
                                        <button
                                            v-if="tenant.status === 'suspended'"
                                            @click.stop.prevent="activateTenant(tenant.id)"
                                            class="text-green-600 hover:text-green-900 text-sm"
                                        >
                                            Activate
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Pagination -->
                <div v-if="pagination && pagination.links" class="mt-4 flex justify-center">
                    <div class="flex space-x-2">
                        <Link
                            v-for="link in pagination.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="{
                                'bg-blue-600 text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-50': !link.active,
                                'opacity-50 cursor-not-allowed': !link.url,
                            }"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    tenants: {
        type: Array,
        default: () => [],
    },
    pagination: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');

const filter = () => {
    router.get(route('tenants.index'), {
        search: search.value,
        status: statusFilter.value,
    }, {
        preserveState: true,
    });
};

const suspendTenant = (id) => {
    if (confirm('Are you sure you want to suspend this tenant?')) {
        router.post(route('tenants.suspend', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};

const activateTenant = (id) => {
    if (confirm('Are you sure you want to activate this tenant?')) {
        router.post(route('tenants.activate', id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};
</script>

