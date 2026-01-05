@extends('admin.layout')

@section('title', 'Tenants - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Tenants</h1>
            <a
                href="{{ route('tenants.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
            >
                Create Tenant
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('tenants.index') }}" class="flex gap-4">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search tenants..."
                    class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                />
                <select
                    name="status"
                    class="border border-gray-300 rounded-md px-3 py-2"
                >
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial</option>
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
                @forelse($tenants as $tenant)
                    <li>
                        <div class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $tenant['status'] === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $tenant['status'] === 'suspended' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $tenant['status'] === 'trial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            "
                                        >
                                            {{ $tenant['status'] ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <a href="{{ route('tenants.show', $tenant['id']) }}" class="block">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $tenant['name'] ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $tenant['email'] ?? 'No email' }}
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(($tenant['status'] ?? '') === 'active')
                                        <form method="POST" action="{{ route('tenants.suspend', $tenant['id']) }}" class="inline" onsubmit="return confirm('Are you sure you want to suspend this tenant?');">
                                            @csrf
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900 text-sm">
                                                Suspend
                                            </button>
                                        </form>
                                    @endif
                                    @if(($tenant['status'] ?? '') === 'suspended')
                                        <form method="POST" action="{{ route('tenants.activate', $tenant['id']) }}" class="inline" onsubmit="return confirm('Are you sure you want to activate this tenant?');">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 text-sm">
                                                Activate
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6">
                        <div class="text-center text-gray-500">No tenants found</div>
                    </li>
                @endforelse
            </ul>
        </div>

        <!-- Pagination -->
        @if(isset($pagination['links']) && count($pagination['links']) > 0)
            <div class="mt-4 flex justify-center">
                <div class="flex space-x-2">
                    @foreach($pagination['links'] as $link)
                        @if($link['url'])
                            <a
                                href="{{ $link['url'] }}"
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium {{ $link['active'] ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }}"
                            >
                                {!! $link['label'] !!}
                            </a>
                        @else
                            <span
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium opacity-50 cursor-not-allowed"
                            >
                                {!! $link['label'] !!}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

