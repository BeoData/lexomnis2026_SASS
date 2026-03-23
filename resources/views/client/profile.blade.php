@extends('client.layout')

@section('title', 'Moj Profil - ' . config('app.name', 'LexOmnis'))

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900">Moj Profil</h2>
    <p class="text-gray-600">Ažurirajte svoje lične podatke i lozinku.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Info Form -->
    <div class="lg:col-span-2">
        <div class="card bg-white shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                <h3 class="text-lg font-semibold text-gray-900">Lični Podaci</h3>
                <span class="text-xs text-blue-600 font-medium bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">Aktivno</span>
            </div>
            <div class="p-6">
                <form action="{{ route('client.profile.update') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Ime i Prezime</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   class="input-field @error('name') border-red-500 @enderror" placeholder="Unesite vaše ime i prezime">
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Adresa</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   class="input-field bg-gray-50/50 @error('email') border-red-500 @enderror" placeholder="vas@email.com">
                            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Promena Lozinke</h4>
                        <p class="text-xs text-gray-500 mb-6 italic">* Ostavite prazno ukoliko ne želite da menjate lozinku</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-2">Trenutna Lozinka</label>
                                <input type="password" id="current_password" name="current_password" 
                                       class="input-field @error('current_password') border-red-500 @enderror" placeholder="••••••••">
                                @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">Nova Lozinka</label>
                                    <input type="password" id="new_password" name="new_password" 
                                           class="input-field @error('new_password') border-red-500 @enderror" placeholder="••••••••">
                                    @error('new_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Potvrda Nove Lozinke</label>
                                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                           class="input-field" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="btn-primary flex items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Sačuvaj Promene
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Sidebar Info -->
    <div class="space-y-6">
        <div class="card bg-gradient-to-br from-blue-600 to-blue-800 text-white border-none shadow-blue-100">
            <h3 class="text-lg font-bold mb-4">Informacije o nalogu</h3>
            <div class="space-y-4">
                <div>
                    <span class="text-blue-200 text-xs font-bold uppercase tracking-widest">Korisnički ID</span>
                    <p class="font-bold">#{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <span class="text-blue-200 text-xs font-bold uppercase tracking-widest">Datum Registracije</span>
                    <p class="font-bold">{{ $user->created_at->format('d.m.Y.') }}</p>
                </div>
                <div>
                    <span class="text-blue-200 text-xs font-bold uppercase tracking-widest">Vrsta Naloga</span>
                    <p class="font-bold bg-blue-400/30 px-3 py-1 rounded inline-block text-sm mt-1">Klijent (LexOmnis Port)</p>
                </div>
            </div>
        </div>
        
        <div class="card border-dashed border-2 border-gray-200 bg-gray-50/30">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Potrebna pomoć?</h3>
            <p class="text-sm text-gray-600 mb-6">Ukoliko imate problema sa nalogom ili pristupom, kontaktirajte naš tim za podršku.</p>
            <a href="mailto:podrska@lexomnis.com" class="btn-secondary w-full text-center flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Podrška Email
            </a>
        </div>
    </div>
</div>
@endsection
