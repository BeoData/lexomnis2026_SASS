@extends('admin.layout')

@section('title', 'Subscription Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('subscriptions.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Subscriptions</a>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscription Details</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tenant</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription['tenant']['name'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Plan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $subscription['plan']['name'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ ($subscription['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ ($subscription['status'] ?? '') === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                            {{ ($subscription['status'] ?? '') === 'expired' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        ">{{ $subscription['status'] ?? 'N/A' }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Price</dt>
                    <dd class="mt-1 text-sm text-gray-900">${{ $subscription['plan']['price'] ?? '0' }} / {{ $subscription['plan']['billing_cycle'] ?? 'N/A' }}</dd>
                </div>
                @if(isset($subscription['started_at']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Started At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($subscription['started_at'])->format('M d, Y h:i A') }}</dd>
                    </div>
                @endif
                @if(isset($subscription['expires_at']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expires At</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($subscription['expires_at'])->format('M d, Y h:i A') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection

