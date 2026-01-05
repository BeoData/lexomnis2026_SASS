<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Users</h1>

                <!-- Filters -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form @submit.prevent="filter" class="flex gap-4">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Search users..."
                            class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                        />
                        <select
                            v-model="roleFilter"
                            class="border border-gray-300 rounded-md px-3 py-2"
                        >
                            <option value="">All Roles</option>
                            <option value="admin">Admin</option>
                            <option value="attorney">Attorney</option>
                            <option value="staff">Staff</option>
                        </select>
                        <button
                            type="submit"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="user in users" :key="user.id">
                            <Link
                                :href="route('users.show', user.id)"
                                class="block hover:bg-gray-50 px-4 py-4 sm:px-6"
                            >
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ user.name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ user.email }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Role: {{ user.role }} | Firm: {{ user.firm?.name || 'N/A' }}
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button
                                            @click.stop.prevent="impersonateUser(user.id)"
                                            :disabled="impersonating[user.id]"
                                            class="text-blue-600 hover:text-blue-900 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <span v-if="impersonating[user.id]">Redirecting...</span>
                                            <span v-else>Impersonate</span>
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
    users: {
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
const roleFilter = ref(props.filters.role || '');

const filter = () => {
    router.get(route('users.index'), {
        search: search.value,
        role: roleFilter.value,
    }, {
        preserveState: true,
    });
};

const impersonating = ref({});

const impersonateUser = (id) => {
    if (confirm('Are you sure you want to impersonate this user? You will be redirected to the tenant application.')) {
        impersonating.value[id] = true;
        router.post(route('users.impersonate', id), {}, {
            onSuccess: () => {
                // Redirect will happen automatically
            },
            onError: (errors) => {
                impersonating.value[id] = false;
                if (errors.error) {
                    alert('Error: ' + errors.error);
                }
            },
            onFinish: () => {
                impersonating.value[id] = false;
            },
        });
    }
};
</script>

