<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('users.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Users
                    </Link>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ user.name }}</h1>
                    <div class="flex space-x-2">
                        <button
                            @click="impersonateUser"
                            :disabled="impersonating"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="impersonating">Redirecting...</span>
                            <span v-else>Impersonate</span>
                        </button>
                        <button
                            @click="resetPassword"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Reset Password
                        </button>
                        <button
                            v-if="user.status === 'active'"
                            @click="suspendUser"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded"
                        >
                            Suspend
                        </button>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Role</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ user.role || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Firm</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ user.firm?.name || 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': user.status === 'active',
                                        'bg-red-100 text-red-800': user.status === 'suspended',
                                    }"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ user.status || 'active' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ user.last_login_at ? new Date(user.last_login_at).toLocaleString() : 'Never' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Pages/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useRoute } from '@/composables/useRoute';

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const { route } = useRoute();
const impersonating = ref(false);

const impersonateUser = () => {
    if (confirm('Are you sure you want to impersonate this user? You will be redirected to the tenant application.')) {
        impersonating.value = true;
        router.post(route('users.impersonate', props.user.id), {}, {
            onSuccess: () => {
                // Redirect will happen automatically
            },
            onError: (errors) => {
                impersonating.value = false;
                if (errors.error) {
                    alert('Error: ' + errors.error);
                }
            },
            onFinish: () => {
                impersonating.value = false;
            },
        });
    }
};

const resetPassword = () => {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        router.post(route('users.reset-password', props.user.id), {}, {
            preserveScroll: true,
            onSuccess: (page) => {
                if (page.props.flash?.success) {
                    alert(page.props.flash.success);
                }
            },
        });
    }
};

const suspendUser = () => {
    if (confirm('Are you sure you want to suspend this user?')) {
        router.post(route('users.suspend', props.user.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                router.reload();
            },
        });
    }
};
</script>

