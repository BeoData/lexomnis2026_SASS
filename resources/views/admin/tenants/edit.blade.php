@extends('admin.layout')

@section('title', 'Edit Tenant - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a
                href="{{ route('tenants.show', $tenant['id']) }}"
                class="text-blue-600 hover:text-blue-800 text-sm"
            >
                ← Back to Tenant
            </a>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Tenant</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('tenants.update', $tenant['id']) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $tenant['name'] ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $tenant['email'] ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Status
                        </label>
                        <select
                            id="status"
                            name="status"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 @enderror"
                        >
                            <option value="active" {{ old('status', $tenant['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ old('status', $tenant['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="trial" {{ old('status', $tenant['status'] ?? '') === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="deleted" {{ old('status', $tenant['status'] ?? '') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                        @error('status')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Phone
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone', $tenant['phone'] ?? '') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-300 @enderror"
                        />
                        @error('phone')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">
                                Country
                            </label>
                            <input
                                id="country"
                                name="country"
                                type="text"
                                value="{{ old('country', $tenant['country'] ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('country') border-red-300 @enderror"
                            />
                            @error('country')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700">
                                Timezone
                            </label>
                            <input
                                id="timezone"
                                name="timezone"
                                type="text"
                                value="{{ old('timezone', $tenant['timezone'] ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('timezone') border-red-300 @enderror"
                            />
                            @error('timezone')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700">
                                Currency
                            </label>
                            <input
                                id="currency"
                                name="currency"
                                type="text"
                                value="{{ old('currency', $tenant['currency'] ?? '') }}"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('currency') border-red-300 @enderror"
                            />
                            @error('currency')
                                <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Subscription Plan Section -->
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Plan</h3>
                        
                        @php
                            $currentPlanId = $tenant['subscription']['plan']['id'] ?? null;
                            $currentBillingPeriod = $tenant['subscription']['plan']['billing_period'] ?? null;
                        @endphp

                        @if(!empty($plans))
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Plan
                                    </label>
                                    <select
                                        id="plan_id"
                                        name="plan_id"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('plan_id') border-red-300 @enderror"
                                    >
                                        <option value="">-- No Plan Selected --</option>
                                        @foreach($plans as $plan)
                                            <option 
                                                value="{{ $plan['id'] }}" 
                                                data-billing-period="{{ $plan['billing_period'] }}"
                                                {{ old('plan_id', $currentPlanId) == $plan['id'] ? 'selected' : '' }}
                                            >
                                                {{ $plan['name'] }} - 
                                                ${{ number_format($plan['price'], 2) }} / 
                                                {{ $plan['billing_period'] === 'monthly' ? 'month' : 'year' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('plan_id')
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div>
                                    <label for="billing_period" class="block text-sm font-medium text-gray-700 mb-2">
                                        Billing Period
                                    </label>
                                    <select
                                        id="billing_period"
                                        name="billing_period"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('billing_period') border-red-300 @enderror"
                                    >
                                        <option value="monthly" {{ old('billing_period', $currentBillingPeriod) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        <option value="yearly" {{ old('billing_period', $currentBillingPeriod) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    </select>
                                    @error('billing_period')
                                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @if($currentPlanId)
                                <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                    <p class="text-sm text-blue-800">
                                        <strong>Current Plan:</strong> 
                                        {{ $tenant['subscription']['plan']['name'] ?? 'N/A' }} 
                                        ({{ $currentBillingPeriod ?? 'N/A' }})
                                    </p>
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">No active plans available.</p>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a
                        href="{{ route('tenants.show', $tenant['id']) }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Update Tenant
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Danger Zone</h2>
            <p class="text-sm text-gray-600 mb-4">
                Once you delete this tenant, there is no going back. Please be certain.
            </p>
            <form method="POST" action="{{ route('tenants.destroy', $tenant['id']) }}" onsubmit="return confirm('Are you sure you want to delete this tenant? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded"
                >
                    Delete Tenant
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('plan_id');
    const billingPeriodSelect = document.getElementById('billing_period');
    
    if (planSelect && billingPeriodSelect) {
        // Filter plans when billing period changes
        billingPeriodSelect.addEventListener('change', function() {
            const selectedPeriod = this.value;
            const options = planSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                
                const planPeriod = option.getAttribute('data-billing-period');
                if (planPeriod === selectedPeriod) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Reset plan selection if current selection doesn't match billing period
            const selectedOption = planSelect.options[planSelect.selectedIndex];
            if (selectedOption.value !== '' && selectedOption.getAttribute('data-billing-period') !== selectedPeriod) {
                planSelect.value = '';
            }
        });
        
        // Update billing period when plan is selected
        planSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value !== '') {
                const planPeriod = selectedOption.getAttribute('data-billing-period');
                if (planPeriod) {
                    billingPeriodSelect.value = planPeriod;
                    // Trigger change event to filter plans
                    billingPeriodSelect.dispatchEvent(new Event('change'));
                }
            }
        });
        
        // Initial filter on page load
        if (billingPeriodSelect.value) {
            billingPeriodSelect.dispatchEvent(new Event('change'));
        }
    }
});
</script>
@endsection

