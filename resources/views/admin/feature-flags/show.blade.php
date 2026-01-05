@extends('admin.layout')

@section('title', 'Feature Flag Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6"><a href="{{ route('feature-flags.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Feature Flags</a></div>
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $featureFlag['name'] ?? 'N/A' }}</h1>
            <a href="{{ route('feature-flags.edit', $featureFlag['id']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Edit</a>
        </div>
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div><dt class="text-sm font-medium text-gray-500">Key</dt><dd class="mt-1 text-sm text-gray-900"><code class="bg-gray-100 px-2 py-1 rounded">{{ $featureFlag['key'] ?? 'N/A' }}</code></dd></div>
                <div><dt class="text-sm font-medium text-gray-500">Status</dt><dd class="mt-1 text-sm text-gray-900"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($featureFlag['enabled'] ?? $featureFlag['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ($featureFlag['enabled'] ?? $featureFlag['is_active'] ?? false) ? 'Active' : 'Inactive' }}</span></dd></div>
                @if(isset($featureFlag['description']))<div><dt class="text-sm font-medium text-gray-500">Description</dt><dd class="mt-1 text-sm text-gray-900">{{ $featureFlag['description'] }}</dd></div>@endif
                <div><dt class="text-sm font-medium text-gray-500">Environment</dt><dd class="mt-1 text-sm text-gray-900 capitalize">{{ $featureFlag['environment'] ?? 'N/A' }}</dd></div>
            </dl>
        </div>
    </div>
</div>
@endsection

