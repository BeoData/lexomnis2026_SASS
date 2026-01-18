@extends('admin.layout')

@section('title', 'Tenant Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a
                href="{{ route('tenants.index') }}"
                class="text-blue-600 hover:text-blue-800 text-sm"
            >
                ← Back to Tenants
            </a>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $tenant['name'] ?? 'N/A' }}</h1>
            <div class="flex space-x-2">
                <a
                    href="{{ route('tenants.edit', $tenant['id']) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                >
                    Edit
                </a>
                @if(($tenant['status'] ?? '') === 'active')
                    <form method="POST" action="{{ route('tenants.suspend', $tenant['id']) }}" class="inline" onsubmit="return confirm('Are you sure you want to suspend this tenant?');">
                        @csrf
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded">
                            Suspend
                        </button>
                    </form>
                @endif
                @if(($tenant['status'] ?? '') === 'suspended')
                    <form method="POST" action="{{ route('tenants.activate', $tenant['id']) }}" class="inline" onsubmit="return confirm('Are you sure you want to activate this tenant?');">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
                            Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ ($tenant['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ ($tenant['status'] ?? '') === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                {{ ($tenant['status'] ?? '') === 'trial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            "
                        >
                            {{ $tenant['status'] ?? 'N/A' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['email'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['phone'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Country</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['country'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Timezone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['timezone'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Currency</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['currency'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $tenant['slug'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Database</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $tenant['db_name'] ?? 'N/A' }}
                        @if(!empty($tenant['db_driver']))
                            ({{ $tenant['db_driver'] }})
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($tenant['created_at']) ? \Carbon\Carbon::parse($tenant['created_at'])->format('M d, Y h:i A') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Subscription Plan Section -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription Plan</h2>
            
            @if(!empty($tenant['subscription']) && !empty($tenant['subscription']['plan']))
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Plan Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">
                            {{ $tenant['subscription']['plan']['name'] ?? 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Price</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            ${{ number_format($tenant['subscription']['plan']['price'] ?? 0, 2) }} / 
                            {{ ($tenant['subscription']['plan']['billing_period'] ?? 'monthly') === 'monthly' ? 'month' : 'year' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Subscription Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ ($tenant['subscription']['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ ($tenant['subscription']['status'] ?? '') === 'trial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ ($tenant['subscription']['status'] ?? '') === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                "
                            >
                                {{ $tenant['subscription']['status'] ?? 'N/A' }}
                            </span>
                        </dd>
                    </div>
                    @if(!empty($tenant['subscription']['trial_ends_at']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Trial Ends At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($tenant['subscription']['trial_ends_at'])->format('M d, Y h:i A') }}
                            </dd>
                        </div>
                    @endif
                    @if(!empty($tenant['subscription']['ends_at']))
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Subscription Ends At</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($tenant['subscription']['ends_at'])->format('M d, Y h:i A') }}
                            </dd>
                        </div>
                    @endif
                </dl>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 mb-4">This tenant doesn't have an active subscription plan.</p>
                    @if(!empty($plans))
                        <form method="POST" action="{{ route('tenants.update', $tenant['id']) }}" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="plan_id" value="{{ $plans[0]['id'] ?? '' }}">
                            <input type="hidden" name="billing_period" value="monthly">
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                            >
                                Assign Plan
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

