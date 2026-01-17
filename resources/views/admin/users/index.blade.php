@extends('admin.layout')

@section('title', 'Users - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Users</h1>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('users.index') }}" class="flex gap-4">
                <select
                    name="tenant_id"
                    class="border border-gray-300 rounded-md px-3 py-2"
                >
                    <option value="">Select Tenant</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant['id'] }}" {{ (request('tenant_id') == $tenant['id']) ? 'selected' : '' }}>
                            {{ $tenant['name'] ?? $tenant['slug'] ?? ('Tenant #' . $tenant['id']) }}
                        </option>
                    @endforeach
                </select>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search users..."
                    class="flex-1 border border-gray-300 rounded-md px-3 py-2"
                />
                <select
                    name="role"
                    class="border border-gray-300 rounded-md px-3 py-2"
                >
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="attorney" {{ request('role') === 'attorney' ? 'selected' : '' }}>Attorney</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
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
                @forelse($users as $user)
                    <li>
                        <div class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('users.show', $user['id'], ['tenant_id' => request('tenant_id')]) }}" class="block">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user['name'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user['email'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Role: {{ $user['role'] ?? 'N/A' }} | Firm: {{ $user['firm']['name'] ?? 'N/A' }}
                                        </div>
                                    </a>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <form method="POST" action="{{ route('users.impersonate', $user['id'], ['tenant_id' => request('tenant_id')]) }}" class="inline" onsubmit="return confirm('Are you sure you want to impersonate this user? You will be redirected to the tenant application.');">
                                        @csrf
                                        <input type="hidden" name="tenant_id" value="{{ request('tenant_id') }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">
                                            Impersonate
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6">
                        <div class="text-center text-gray-500">
                            {{ request('tenant_id') ? 'No users found' : 'Select a tenant to view users' }}
                        </div>
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

