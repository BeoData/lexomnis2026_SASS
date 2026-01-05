@extends('admin.layout')

@section('title', 'Plan Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('plans.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Plans</a>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $plan['name'] ?? 'N/A' }}</h1>
            <a href="{{ route('plans.edit', $plan['id']) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Edit</a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($plan['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ($plan['is_active'] ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Price</dt>
                    <dd class="mt-1 text-sm text-gray-900">${{ $plan['price'] ?? '0' }} / {{ ($plan['billing_cycle'] ?? 'monthly') === 'monthly' ? 'month' : 'year' }}</dd>
                </div>
                @if(isset($plan['description']))
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $plan['description'] }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Billing Cycle</dt>
                    <dd class="mt-1 text-sm text-gray-900 capitalize">{{ $plan['billing_cycle'] ?? 'monthly' }}</dd>
                </div>
                @if(isset($plan['features']) && is_array($plan['features']) && count($plan['features']) > 0)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Features</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <ul class="list-disc list-inside">
                                @foreach($plan['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($plan['created_at']) ? \Carbon\Carbon::parse($plan['created_at'])->format('M d, Y h:i A') : 'N/A' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

