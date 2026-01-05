@extends('admin.layout')

@section('title', 'Create Feature Flag - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6"><a href="{{ route('feature-flags.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Feature Flags</a></div>
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Create Feature Flag</h1>
        <div class="bg-white shadow rounded-lg p-6">
            <form method="POST" action="{{ route('feature-flags.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <div><label for="name" class="block text-sm font-medium text-gray-700">Name *</label><input id="name" name="name" type="text" required value="{{ old('name') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror" />@error('name')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror</div>
                    <div><label for="key" class="block text-sm font-medium text-gray-700">Key *</label><input id="key" name="key" type="text" required value="{{ old('key') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('key') border-red-300 @enderror" />@error('key')<div class="mt-1 text-sm text-red-600">{{ $message }}</div>@enderror</div>
                    <div><label for="description" class="block text-sm font-medium text-gray-700">Description</label><textarea id="description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea></div>
                    <div><label for="environment" class="block text-sm font-medium text-gray-700">Environment *</label><select id="environment" name="environment" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"><option value="all" {{ old('environment') === 'all' ? 'selected' : '' }}>All</option><option value="production" {{ old('environment') === 'production' ? 'selected' : '' }}>Production</option><option value="staging" {{ old('environment') === 'staging' ? 'selected' : '' }}>Staging</option><option value="development" {{ old('environment') === 'development' ? 'selected' : '' }}>Development</option></select></div>
                    <div><label class="flex items-center"><input name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" /><span class="ml-2 text-sm text-gray-700">Active</span></label></div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('feature-flags.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Create Feature Flag</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

