@extends('admin.layout')

@section('title', 'System Monitoring - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">System Monitoring</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Health Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">System Health</h2>
                @if($health)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status:</span>
                            <span class="text-sm font-medium {{ ($health['status'] ?? '') === 'healthy' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $health['status'] ?? 'Unknown' }}
                            </span>
                        </div>
                        @if(isset($health['version']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Version:</span>
                                <span class="text-sm text-gray-900">{{ $health['version'] }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Unable to fetch health status
                    </div>
                @endif
                <a
                    href="{{ route('system.health') }}"
                    class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm"
                >
                    View Details →
                </a>
            </div>

            <!-- Metrics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Performance Metrics</h2>
                @if($metrics)
                    <div class="space-y-2">
                        @if(isset($metrics['response_time']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Response Time:</span>
                                <span class="text-sm text-gray-900">{{ $metrics['response_time'] }}ms</span>
                            </div>
                        @endif
                        @if(isset($metrics['memory_usage']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Memory Usage:</span>
                                <span class="text-sm text-gray-900">{{ $metrics['memory_usage'] }}MB</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Unable to fetch metrics
                    </div>
                @endif
                <a
                    href="{{ route('system.metrics') }}"
                    class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm"
                >
                    View Details →
                </a>
            </div>

            <!-- Queue Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Queue Status</h2>
                @if($queues)
                    <div class="space-y-2">
                        @if(isset($queues['pending']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Pending:</span>
                                <span class="text-sm text-gray-900">{{ $queues['pending'] }}</span>
                            </div>
                        @endif
                        @if(isset($queues['failed']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Failed:</span>
                                <span class="text-sm text-red-600">{{ $queues['failed'] }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Unable to fetch queue status
                    </div>
                @endif
            </div>

            <!-- Cron Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Cron Status</h2>
                @if($crons)
                    <div class="space-y-2">
                        @if(isset($crons['last_run']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Last Run:</span>
                                <span class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($crons['last_run'])->format('M d, Y h:i A') }}
                                </span>
                            </div>
                        @endif
                        @if(isset($crons['status']))
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="text-sm font-medium {{ ($crons['status'] ?? '') === 'running' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ $crons['status'] ?? 'Unknown' }}
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        Unable to fetch cron status
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

