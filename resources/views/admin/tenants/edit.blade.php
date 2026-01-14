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
@endsection

