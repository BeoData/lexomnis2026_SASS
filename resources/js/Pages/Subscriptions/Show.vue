<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="mb-6">
                    <Link
                        :href="route('subscriptions.index')"
                        class="text-blue-600 hover:text-blue-800 text-sm"
                    >
                        ← Back to Subscriptions
                    </Link>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscription Details</h1>

                <div class="bg-white shadow rounded-lg p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ subscription.tenant?.name || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Plan</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ subscription.plan?.name || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span
                                    :class="{
                                        'bg-green-100 text-green-800': subscription.status === 'active',
                                        'bg-red-100 text-red-800': subscription.status === 'cancelled',
                                        'bg-yellow-100 text-yellow-800': subscription.status === 'expired',
                                    }"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                >
                                    {{ subscription.status }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                ${{ subscription.plan?.price || '0' }} / {{ subscription.plan?.billing_cycle || 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Started At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ subscription.started_at ? new Date(subscription.started_at).toLocaleString() : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Expires At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ subscription.expires_at ? new Date(subscription.expires_at).toLocaleString() : 'N/A' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ subscription.created_at ? new Date(subscription.created_at).toLocaleString() : 'N/A' }}
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
import { Link } from '@inertiajs/vue3';

defineProps({
    subscription: {
        type: Object,
        required: true,
    },
});
</script>

