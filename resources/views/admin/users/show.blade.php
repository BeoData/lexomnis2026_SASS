@extends('admin.layout')

@section('title', 'User Details - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a
                href="{{ route('users.index', ['tenant_id' => $tenant_id]) }}"
                class="text-blue-600 hover:text-blue-800 text-sm"
            >
                ← Back to Users
            </a>
        </div>

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $user['name'] ?? 'N/A' }}</h1>
            <div class="flex space-x-2">
                <form method="POST" action="{{ route('users.impersonate', $user['id'], ['tenant_id' => $tenant_id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to impersonate this user? You will be redirected to the tenant application.');">
                    @csrf
                    <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Impersonate
                    </button>
                </form>
                <form method="POST" action="{{ route('users.reset-password', $user['id'], ['tenant_id' => $tenant_id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to reset this user\'s password?');">
                    @csrf
                    <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">
                    <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded">
                        Reset Password
                    </button>
                </form>
                @if(($user['status'] ?? 'active') === 'active')
                    <form method="POST" action="{{ route('users.suspend', $user['id'], ['tenant_id' => $tenant_id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to suspend this user?');">
                        @csrf
                        <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded">
                            Suspend
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user['email'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Role</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user['role'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Firm</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $user['firm']['name'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ ($user['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                            "
                        >
                            {{ $user['status'] ?? 'active' }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($user['created_at']) ? \Carbon\Carbon::parse($user['created_at'])->format('M d, Y h:i A') : 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ isset($user['last_login_at']) ? \Carbon\Carbon::parse($user['last_login_at'])->format('M d, Y h:i A') : 'Never' }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

