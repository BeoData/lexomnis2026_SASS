@extends('admin.layout')

@section('title', 'Feature Flags - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Feature Flags</h1>
            <a href="{{ route('feature-flags.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Create Feature Flag</a>
        </div>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('feature-flags.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search feature flags..." class="flex-1 border border-gray-300 rounded-md px-3 py-2" />
                <select name="is_active" class="border border-gray-300 rounded-md px-3 py-2">
                    <option value="">All Statuses</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded">Filter</button>
            </form>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($featureFlags as $flag)
                    <li>
                        <a href="{{ route('feature-flags.show', $flag['id']) }}" class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $flag['name'] ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500"><code>{{ $flag['key'] ?? 'N/A' }}</code></div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($flag['enabled'] ?? $flag['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ($flag['enabled'] ?? $flag['is_active'] ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6"><div class="text-center text-gray-500">No feature flags found</div></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

