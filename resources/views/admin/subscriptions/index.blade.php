@extends('admin.layout')

@section('title', 'Subscriptions - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Subscriptions</h1>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('subscriptions.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subscriptions..." class="flex-1 border border-gray-300 rounded-md px-3 py-2" />
                <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded">Filter</button>
            </form>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                    <li>
                        <a href="{{ route('subscriptions.show', $subscription['id']) }}" class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $subscription['tenant']['name'] ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">Plan: {{ $subscription['plan']['name'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        Started: {{ isset($subscription['started_at']) ? \Carbon\Carbon::parse($subscription['started_at'])->format('M d, Y') : 'N/A' }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ ($subscription['status'] ?? '') === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ ($subscription['status'] ?? '') === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ ($subscription['status'] ?? '') === 'expired' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    ">{{ $subscription['status'] ?? 'N/A' }}</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">${{ $subscription['plan']['price'] ?? '0' }}</div>
                                        <div class="text-xs text-gray-500">{{ $subscription['plan']['billing_cycle'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6">
                        <div class="text-center text-gray-500">No subscriptions found</div>
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

