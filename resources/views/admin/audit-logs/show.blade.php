@extends('admin.layout')

@section('title', 'Audit Log Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6"><a href="{{ route('audit-logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Audit Logs</a></div>
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Audit Log Details</h1>
        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div><dt class="text-sm font-medium text-gray-500">Action</dt><dd class="mt-1 text-sm text-gray-900">{{ $auditLog['action'] ?? 'N/A' }}</dd></div>
                <div><dt class="text-sm font-medium text-gray-500">Description</dt><dd class="mt-1 text-sm text-gray-900">{{ $auditLog['description'] ?? 'N/A' }}</dd></div>
                @if(isset($auditLog['user']))<div><dt class="text-sm font-medium text-gray-500">User</dt><dd class="mt-1 text-sm text-gray-900">{{ $auditLog['user']['name'] ?? $auditLog['user']['email'] ?? 'N/A' }}</dd></div>@endif
                <div><dt class="text-sm font-medium text-gray-500">Created At</dt><dd class="mt-1 text-sm text-gray-900">{{ isset($auditLog['created_at']) ? \Carbon\Carbon::parse($auditLog['created_at'])->format('M d, Y h:i A') : 'N/A' }}</dd></div>
                @if(isset($auditLog['ip_address']))<div><dt class="text-sm font-medium text-gray-500">IP Address</dt><dd class="mt-1 text-sm text-gray-900">{{ $auditLog['ip_address'] }}</dd></div>@endif
                @if(isset($auditLog['user_agent']))<div><dt class="text-sm font-medium text-gray-500">User Agent</dt><dd class="mt-1 text-sm text-gray-900">{{ $auditLog['user_agent'] }}</dd></div>@endif
            </dl>
            @if(isset($auditLog['properties']) || isset($auditLog['changes']))
                <div class="mt-6">
                    <dt class="text-sm font-medium text-gray-500 mb-2">Properties</dt>
                    <dd class="mt-1"><pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($auditLog['properties'] ?? $auditLog['changes'] ?? [], JSON_PRETTY_PRINT) }}</pre></dd>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

