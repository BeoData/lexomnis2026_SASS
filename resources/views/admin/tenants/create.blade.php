@extends('admin.layout')

@section('title', 'Create Tenant - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a
                href="{{ route('tenants.index') }}"
                class="text-blue-600 hover:text-blue-800 text-sm"
            >
                ← Back to Tenants
            </a>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Tenant</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('tenants.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Name *
                        </label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required
                            value="{{ old('name') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                        />
                        @error('name')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                        @if($errors->has('error'))
                            <div class="mt-1 text-sm text-red-600">{{ $errors->first('error') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email *
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            value="{{ old('email') }}"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                        />
                        @error('email')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password *
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @enderror"
                        />
                        @error('password')
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
                            value="{{ old('phone') }}"
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
                                value="{{ old('country', 'RS') }}"
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
                                value="{{ old('timezone', 'Europe/Belgrade') }}"
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
                                value="{{ old('currency', 'RSD') }}"
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
                        href="{{ route('tenants.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded"
                    >
                        Create Tenant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

