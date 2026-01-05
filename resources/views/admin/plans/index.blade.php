@extends('admin.layout')

@section('title', 'Plans - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Plans</h1>
            <a href="{{ route('plans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                Create Plan
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($plans as $plan)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $plan['name'] ?? 'N/A' }}</h3>
                            @if(isset($plan['description']))
                                <p class="text-sm text-gray-600 mt-1">{{ $plan['description'] }}</p>
                            @endif
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($plan['is_active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ($plan['is_active'] ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <div class="text-2xl font-bold text-gray-900">${{ $plan['price'] ?? '0' }}</div>
                        <div class="text-sm text-gray-600">/ {{ ($plan['billing_cycle'] ?? 'monthly') === 'monthly' ? 'month' : 'year' }}</div>
                    </div>

                    @if(isset($plan['features']) && is_array($plan['features']) && count($plan['features']) > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Features:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                @foreach($plan['features'] as $feature)
                                    <li>• {{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex space-x-2">
                        <a href="{{ route('plans.show', $plan['id']) }}" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded text-center text-sm">
                            View
                        </a>
                        <a href="{{ route('plans.edit', $plan['id']) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center text-sm">
                            Edit
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500">No plans found</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

