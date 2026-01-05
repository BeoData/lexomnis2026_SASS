@extends('admin.layout')

@section('title', 'System Health - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('system.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                ← Back to System Monitoring
            </a>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">System Health</h1>

        <div class="bg-white shadow rounded-lg p-6">
            @if($health)
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    @foreach($health as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if(is_array($value))
                                    <pre class="text-xs">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                @elseif(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <div class="text-center text-gray-500 py-8">
                    Unable to fetch system health information
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

