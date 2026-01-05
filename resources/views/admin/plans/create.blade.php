@extends('admin.layout')

@section('title', 'Create Plan - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('plans.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Plans</a>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Plan</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('plans.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" />
                        @error('name')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700">Price *</label>
                            <input id="price" name="price" type="number" step="0.01" min="0" required value="{{ old('price') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-300 @enderror" />
                            @error('price')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label for="billing_cycle" class="block text-sm font-medium text-gray-700">Billing Cycle *</label>
                            <select id="billing_cycle" name="billing_cycle" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="monthly" {{ old('billing_cycle') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="yearly" {{ old('billing_cycle') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
                            <span class="ml-2 text-sm text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('plans.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Create Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

