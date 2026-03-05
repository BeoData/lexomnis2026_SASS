@extends('admin.layout')

@section('title', 'Plans - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-900">Plans</h1>
            
            <div class="flex items-center space-x-4">
                <!-- Billing Period Toggle -->
                <div class="bg-gray-100 p-1 rounded-lg inline-flex">
                    <a href="{{ route('plans.index', array_merge(request()->query(), ['billing_period' => 'monthly'])) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ ($billingPeriod ?? 'monthly') === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Monthly
                    </a>
                    <a href="{{ route('plans.index', array_merge(request()->query(), ['billing_period' => 'yearly'])) }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ ($billingPeriod ?? 'monthly') === 'yearly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                        Yearly
                    </a>
                </div>

                <a href="{{ route('plans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Create Plan
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch">
            @forelse($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden flex flex-col relative focus-within:ring-2 focus-within:ring-blue-500">
                    
                    @if(isset($plan['metadata']['is_popular']) && $plan['metadata']['is_popular'])
                        <div class="absolute top-0 right-0 py-1 px-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider rounded-bl-lg rounded-tr-xl z-10 shadow">
                            Most Popular
                        </div>
                    @endif

                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $plan['name'] ?? 'N/A' }}</h3>
                                @if(isset($plan['description']))
                                    <p class="text-sm text-gray-500 mt-2 line-clamp-2 h-10">{{ $plan['description'] }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-6 flex items-baseline text-gray-900">
                            @php
                                $billingPeriod = $plan['billing_period'] ?? $plan['billing_cycle'] ?? 'monthly';
                                $price = $plan['price'] ?? 0;
                            @endphp
                            <span class="text-5xl font-extrabold tracking-tight">${{ number_format($price, $price == floor($price) ? 0 : 2) }}</span>
                            <span class="ml-1 text-xl font-semibold text-gray-500">/{{ $billingPeriod === 'monthly' ? 'mo' : 'yr' }}</span>
                        </div>

                        <div class="mb-4">
                             <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide uppercase {{ ($plan['is_active'] ?? false) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ($plan['is_active'] ?? false) ? 'Active Plan' : 'Inactive Plan' }}
                            </span>
                        </div>

                        <div class="my-6 border-t border-gray-100"></div>

                        @if(isset($plan['features']) && is_array($plan['features']) && count($plan['features']) > 0)
                            <div class="flex-1">
                                <ul class="space-y-4">
                                    @foreach($plan['features'] as $feature)
                                        <li class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <svg class="h-6 w-6 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <p class="ml-3 text-sm text-gray-700 leading-tight pt-0.5">{{ $feature }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="flex-1 text-gray-400 text-sm italic py-4">
                                No specific features defined.
                            </div>
                        @endif
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-100 mt-auto">
                        <div class="flex space-x-3">
                            <a href="{{ route('plans.show', $plan['id']) }}" class="flex-1 flex justify-center items-center px-4 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                                Details
                            </a>
                            <a href="{{ route('plans.edit', $plan['id']) }}" class="flex-1 flex justify-center items-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Edit Plan
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full border-2 border-dashed border-gray-300 rounded-xl p-16 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No pricing plans yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Get started by creating your first subscription package.</p>
                    <div class="mt-8">
                        <a href="{{ route('plans.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Create First Plan
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

