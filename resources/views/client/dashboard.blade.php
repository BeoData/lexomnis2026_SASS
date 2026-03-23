@extends('client.layout')

@section('title', 'Moj Nalog - Dashboard')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Dobrodošli, {{ $user->name }}</h2>
    <p class="text-gray-600">Upravljajte svojim profilom i pretplatom na LexOmnis platformi.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Profile Overview Card -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Moj Profil</h3>
            <a href="{{ route('client.profile') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Uredi profil →</a>
        </div>
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Ime i Prezime</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-gray-100 p-2 rounded-lg">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase font-semibold">Email Adresa</p>
                    <p class="text-sm font-medium text-gray-900">{{ $user->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Overview Card -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Moji Paketi</h3>
            <a href="{{ route('client.subscription') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Detalji paketa →</a>
        </div>
        
        @if($subscription)
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <span class="text-xl font-bold text-gray-900">{{ $subscription['plan']['name'] ?? 'Pretplata' }}</span>
                    <span class="badge {{ ($subscription['status'] ?? '') == 'active' ? 'badge-green' : 'badge-yellow' }}">
                        {{ ucfirst($subscription['status'] ?? 'Aktivan') }}
                    </span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    @if(isset($subscription['plan']['price']))
                        Cena: €{{ $subscription['plan']['price'] }} / {{ $subscription['plan']['billing_period'] ?? 'mesec' }}
                    @else
                        Podaci se osvežavaju...
                    @endif
                </p>
            </div>

            @if(isset($subscription['trial_ends_at']))
            <div class="bg-blue-50 p-3 rounded-lg mb-4 text-xs text-blue-800">
                Vaš probni period ističe: <strong>{{ \Carbon\Carbon::parse($subscription['trial_ends_at'])->format('d.m.Y.') }}</strong>
            </div>
            @endif

            <a href="{{ route('client.subscription') }}" class="btn-primary w-full text-center">Prikaži Detaljnije</a>
        @else
            <div class="py-2">
                <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 mb-4">
                    <p class="text-gray-600 text-sm mb-2 font-medium">Paket: <span class="text-gray-400">Učitavanje...</span></p>
                    <p class="text-xs text-gray-400">Ukoliko podaci o paketu nisu vidljivi, možete ih proveriti direktno u aplikaciji.</p>
                </div>
                <a href="{{ $tenantAppUrl }}/login" target="_blank" class="btn-primary w-full text-center">Uđi u aplikaciju i Proveri Paket</a>
                
                <p class="text-[10px] text-gray-400 mt-4 text-center">Napomena: Paket je obavezan deo registracije. Ukoliko smatrate da je došlo do greške, kontaktirajte podršku.</p>
            </div>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<h3 class="text-lg font-bold text-gray-900 mb-4">Brze Akcije</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ $tenantAppUrl }}/login" target="_blank" class="card hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="bg-blue-100 p-3 rounded-xl">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
        </div>
        <div>
            <p class="font-bold text-gray-900">Uđi u aplikaciju</p>
            <p class="text-xs text-gray-500">Pokreni radno okruženje</p>
        </div>
    </a>

    <a href="{{ route('client.subscription') }}" class="card hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="bg-green-100 p-3 rounded-xl">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <div>
            <p class="font-bold text-gray-900">Istorija plaćanja</p>
            <p class="text-xs text-gray-500">Pregledaj sve račune</p>
        </div>
    </a>

    <a href="mailto:support@lexomnis.com" class="card hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="bg-purple-100 p-3 rounded-xl">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
        </div>
        <div>
            <p class="font-bold text-gray-900">Podrška</p>
            <p class="text-xs text-gray-500">Pošalji nam upit</p>
        </div>
    </a>
</div>
@endsection
