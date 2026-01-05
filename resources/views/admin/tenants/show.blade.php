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
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($tenant['created_at']) ? \Carbon\Carbon::parse($tenant['created_at'])->format('M d, Y h:i A') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

