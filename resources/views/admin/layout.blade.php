<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'LexOmnis Super Admin'))</title>
    @vite(['resources/css/app.css'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-900">LexOmnis Super Admin</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-1">
                            <!-- Dashboard -->
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                                style="padding-top: 22px;"
                            >
                                Dashboard
                            </a>

                            <!-- Management Dropdown -->
                            <div class="relative group">
                                <button
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('tenants.*', 'users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                                    style="padding-top: 22px;"
                                >
                                    Management
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <a
                                            href="{{ route('tenants.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('tenants.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Tenants
                                        </a>
                                        <a
                                            href="{{ route('users.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Users
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Billing Dropdown -->
                            <div class="relative group">
                                <button
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('plans.*', 'subscriptions.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                                    style="padding-top: 22px;"
                                >
                                    Billing
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <a
                                            href="{{ route('plans.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('plans.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Plans
                                        </a>
                                        <a
                                            href="{{ route('subscriptions.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('subscriptions.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Subscriptions
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Monitoring Dropdown -->
                            <div class="relative group">
                                <button
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('system.*', 'audit-logs.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                                    style="padding-top: 22px;"
                                >
                                    Monitoring
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <a
                                            href="{{ route('system.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('system.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            System Health
                                        </a>
                                        <a
                                            href="{{ route('audit-logs.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('audit-logs.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Audit Logs
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuration Dropdown -->
                            <div class="relative group">
                                <button
                                    class="inline-flex items-center px-3 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('settings.*', 'feature-flags.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }}"
                                    style="padding-top: 22px;"
                                >
                                    Configuration
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                    <div class="py-1">
                                        <a
                                            href="{{ route('settings.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('settings.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Settings
                                        </a>
                                        <a
                                            href="{{ route('feature-flags.index') }}"
                                            class="block px-4 py-2 text-sm {{ request()->routeIs('feature-flags.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100' }}"
                                        >
                                            Feature Flags
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-700 mr-4">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="text-sm text-gray-500 hover:text-gray-700"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <div class="ml-3">
                        @foreach($errors->all() as $error)
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>

