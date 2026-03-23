<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Moj Nalog - ' . config('app.name', 'LexOmnis'))</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css'])
    @endif
    <style>
        .client-nav-link {
            transition: all 0.2s ease;
        }
        .client-nav-link:hover {
            background-color: rgba(59, 130, 246, 0.08);
        }
        .client-nav-link.active {
            background-color: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
            border-left: 3px solid #3b82f6;
        }
        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            padding: 1.5rem;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .btn-secondary {
            background-color: white;
            color: #374151;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: 1px solid #d1d5db;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }
        .btn-secondary:hover {
            background-color: #f9fafb;
        }
        .input-field {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .badge-green { background-color: #dcfce7; color: #166534; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-yellow { background-color: #fef9c3; color: #854d0e; }
        .badge-red { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Top Navigation Bar -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">
                            <a href="{{ route('client.dashboard') }}" class="flex items-center gap-2">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                LexOmnis
                            </a>
                        </h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                                Odjavi se
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto flex">
            <!-- Sidebar Navigation -->
            <aside class="w-64 min-h-[calc(100vh-4rem)] bg-white border-r border-gray-200 py-6 px-3 hidden md:block">
                <div class="space-y-1">
                    <a href="{{ route('client.dashboard') }}"
                       class="client-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 {{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Početna
                    </a>
                    <a href="{{ route('client.profile') }}"
                       class="client-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 {{ request()->routeIs('client.profile') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Moj Profil
                    </a>
                    <a href="{{ route('client.subscription') }}"
                       class="client-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 {{ request()->routeIs('client.subscription') ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Moja Pretplata
                    </a>
                </div>

                @if(isset($tenantAppUrl) && $tenantAppUrl)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Eksterna Aplikacija</p>
                    <a href="{{ $tenantAppUrl }}/login"
                       target="_blank"
                       class="client-nav-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Otvori Aplikaciju →
                    </a>
                </div>
                @endif
            </aside>

            <!-- Main Content -->
            <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                @foreach($errors->all() as $error)
                                    <p class="text-sm text-red-800">{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
