@extends('admin.layout')

@section('title', 'Audit Logs - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Audit Logs</h1>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($auditLogs as $log)
                    <li>
                        <a href="{{ route('audit-logs.show', $log['id']) }}" class="block hover:bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $log['description'] ?? $log['action'] ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if(isset($log['user']))by {{ $log['user']['name'] ?? $log['user']['email'] ?? 'Unknown' }}@endif
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ isset($log['created_at']) ? \Carbon\Carbon::parse($log['created_at'])->format('M d, Y h:i A') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6"><div class="text-center text-gray-500">No audit logs found</div></li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

