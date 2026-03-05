@extends('admin.layout')

@section('title', 'Roles & Permissions - ' . config('app.name'))

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('settings.index') }}" class="text-blue-600 hover:text-blue-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Roles & Permissions System</h1>
                </div>
            </div>

            @if($apiError)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">API Connection Issue</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ $apiError }}</p>
                                <p class="mt-1">Please check if the Tenant Application is running on port 8001 and the API Token is valid.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Roles Overview -->
            <div class="bg-white shadow rounded-lg overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900">System Roles Overview (Tenant Blueprints)</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6 italic">
                        These roles and permissions are automatically seeded into every new tenant database during creation. 
                        Manage these templates to define what new office administrators and staff can access by default.
                    </p>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Scope</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($roles as $name => $roleItems)
                                    @php $role = $roleItems[0] ?? null; @endphp
                                    @if($role)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 uppercase tracking-wider">
                                                {{ ucfirst(str_replace('_', ' ', $name)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $role['description'] ?? 'System defined role' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ count($role['permissions'] ?? []) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($name === 'super_admin')
                                                <span class="text-red-500 text-xs font-semibold flex items-center justify-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                                    SASS Only
                                                </span>
                                            @else
                                                <span class="text-green-600 text-xs font-semibold flex items-center justify-center gap-1">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.707 14.707l4-4a1 1 0 00-1.414-1.414L10 12.586 7.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0z" /></svg>
                                                    Tenant
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                            No roles data found from Tenant API.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Permissions Matrix -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900">Permissions Matrix by Module</h2>
                </div>
                <div class="p-6">
                    @foreach($modules as $module)
                        <div class="mb-10 last:mb-0">
                            <h3 class="text-md font-bold text-gray-900 border-b border-gray-100 pb-2 mb-4 flex items-center">
                                <span class="bg-gray-100 p-2 rounded mr-3">
                                    <i class="{{ $module['icon'] ?? 'fas fa-cube' }} text-gray-500"></i>
                                </span>
                                {{ $module['display_name'] }} 
                                <span class="ml-2 text-xs font-normal text-gray-400">({{ $module['name'] }})</span>
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($module['permissions'] as $permission)
                                    <div class="p-3 border border-gray-100 rounded-md bg-gray-50 hover:bg-white hover:shadow-sm transition-all group">
                                        <div class="font-bold text-xs text-blue-600 group-hover:text-blue-700">{{ $permission['display_name'] }}</div>
                                        <div class="text-[10px] text-gray-400 mt-1 font-mono line-clamp-1">{{ $permission['name'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if(empty($modules))
                        <div class="text-center py-10 text-sm text-gray-500">
                            No permissions data found from Tenant API.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
