@extends('client.layout')

@section('title', 'Moja Pretplata - ' . config('app.name', 'LexOmnis'))

@section('content')
<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Moja Pretplata</h2>
        <p class="text-gray-600">Pregledajte detalje svog paketa i istoriju plaćanja.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ $tenantAppUrl }}/login" target="_blank" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Uđi u Aplikaciju
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Subscription Card -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Current Plan Overview -->
        <div class="card bg-white shadow-sm hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="bg-blue-600 p-4 rounded-2xl shadow-blue-100 shadow-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $subscription['plan']['name'] ?? 'Naziv paketa' }}</h3>
                        <p class="text-sm text-gray-500">Vaša trenutna pretplata</p>
                    </div>
                </div>
                <div class="text-right">
                    @php
                    $successStatuses = ['active', 'paid', 'completed', 'succeeded'];
                    $subStatus = $subscription['status'] ?? 'pending';
                @endphp
                <span class="badge {{ in_array($subStatus, $successStatuses) ? 'badge-green' : 'badge-yellow' }}">
                    {{ ucfirst($subStatus) }}
                </span>
                <p class="text-2xl font-black text-gray-900 mt-2">€{{ $subscription['plan']['price'] ?? 0 }}<span class="text-sm text-gray-400 font-medium">/{{ $subscription['plan']['billing_period'] ?? 'mesec' }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t border-gray-100">
                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Datum Početka</span>
                    <p class="font-bold text-gray-900 mt-1">{{ isset($subscription['created_at']) ? \Carbon\Carbon::parse($subscription['created_at'])->format('d.m.Y.') : 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Sledeća Naplata</span>
                    <p class="font-bold text-gray-900 mt-1">{{ isset($subscription['ends_at']) ? \Carbon\Carbon::parse($subscription['ends_at'])->format('d.m.Y.') : 'Automatski' }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-widest">Način Plaćanja</span>
                    <p class="font-bold text-gray-900 mt-1">{{ ucfirst($subscription['payment_method'] ?? 'Kartica') }}</p>
                </div>
            </div>
        </div>

        <!-- Available Upgrades Section -->
        @if(!empty($plans))
        <div class="space-y-4">
            <h3 class="text-lg font-bold text-gray-900">Dostupne Nadogradnje</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $currentPlanId = $subscription['plan']['id'] ?? null;
                    $currentPlanName = $subscription['plan']['name'] ?? '';
                    $currentPlanPrice = (float)($subscription['plan']['price'] ?? 0);
                @endphp
                
                @foreach($plans as $plan)
                    @php 
                        $planId = $plan['id'] ?? null;
                        $planPrice = (float)($plan['price'] ?? 0);
                        $planName = $plan['name'] ?? '';
                        
                        // LOGIC: Hide if it's the SAME plan, OR if it's a Trial/Free plan 
                        // (Free plans are already picked or not upgrades)
                        $isCurrent = ($planId && $currentPlanId && $planId == $currentPlanId);
                        $isFree = ($planPrice <= 0);
                        $isTrial = (bool)stripos($planName, 'Trial');
                    @endphp

                    @if(!$isCurrent && !$isFree && !$isTrial && $planPrice >= $currentPlanPrice)
                    <div class="card bg-white border border-gray-100 hover:border-blue-200 transition-all flex justify-between items-center group">
                        <div class="flex items-center gap-4">
                            <div class="w-1.5 h-10 bg-gray-200 group-hover:bg-blue-500 rounded-full transition-colors"></div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $planName }}</h4>
                                <p class="text-xs text-gray-500">€{{ number_format($planPrice, 2) }} / {{ $plan['billing_period'] ?? 'mesec' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('checkout', $planId) }}" class="btn-secondary text-sm font-bold bg-blue-50 text-blue-600 border-none hover:bg-blue-600 hover:text-white transition-colors">Izaberi</a>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <div class="card bg-white shadow-sm overflow-hidden p-0">
            <div class="p-6 border-b border-gray-100 bg-gray-50/30">
                <h3 class="text-lg font-bold text-gray-900">Istorija Plaćanja</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-gray-50/50 text-gray-400 font-bold border-b border-gray-100">
                        <tr>
                            <th class="p-4 uppercase tracking-widest text-xs">Datum</th>
                            <th class="p-4 uppercase tracking-widest text-xs">Opis</th>
                            <th class="p-4 uppercase tracking-widest text-xs">Iznos</th>
                            <th class="p-4 uppercase tracking-widest text-xs">Status</th>
                            <th class="p-4 uppercase tracking-widest text-xs text-right">Faktura</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @php
                            $paidStatuses = ['paid', 'completed', 'succeeded', 'approved'];
                        @endphp
                        @if(!empty($invoices))
                            @foreach($invoices as $invoice)
                            @php
                                $invStatus = $invoice['status'] ?? 'pending';
                                $isPaid = in_array(strtolower($invStatus), $paidStatuses);
                                $receiptUrl = $invoice['invoice_pdf'] ?? $invoice['receipt_url'] ?? $invoice['stripe_invoice_pdf'] ?? null;
                                $description = $invoice['description']
                                    ?? ($invoice['plan']['name'] ?? null)
                                    ?? 'Pretplata';
                            @endphp
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="p-4 font-medium text-gray-900">{{ \Carbon\Carbon::parse($invoice['created_at'])->format('d.m.Y.') }}</td>
                                <td class="p-4 text-gray-600">{{ $description }}</td>
                                <td class="p-4 font-bold text-gray-900">
                                    {{ $invoice['currency'] ?? '€' }}{{ number_format((float)($invoice['amount'] ?? 0), 2) }}
                                </td>
                                <td class="p-4">
                                    <span class="badge {{ $isPaid ? 'badge-green' : 'badge-yellow' }}">
                                        {{ ucfirst($invStatus) }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    @if($receiptUrl)
                                        <a href="{{ $receiptUrl }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            PDF
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-500 italic">Još uvek nema zabeleženih plaćanja.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Features & Info Sidebar -->
    <div class="space-y-6">
        <div class="card bg-gray-900 text-white border-none shadow-xl">
            <h4 class="text-sm font-black text-blue-400 uppercase tracking-widest mb-6 border-b border-gray-800 pb-4">Uključenih Funkcija</h4>
            <ul class="space-y-3">
                @if(isset($subscription['plan']['metadata']['features']))
                    @foreach($subscription['plan']['metadata']['features'] as $feature)
                    <li class="flex items-start gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-300">{{ $feature }}</span>
                    </li>
                    @endforeach
                @else
                    <li class="flex items-start gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-300">Pristup svim osnovnim modulima</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-300">Bezbedno skladište podataka</span>
                    </li>
                    <li class="flex items-start gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-300">Email podrška podrška</span>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card bg-white border-2 border-dashed border-gray-200 py-6 text-center">
            <h4 class="font-bold text-gray-900">Otkaži pretplatu?</h4>
            <p class="text-xs text-gray-500 mt-2 mb-4">Ukoliko želite da otkažete pretplatu, kontaktirajte prodajni tim.</p>
            <a href="mailto:sales@lexomnis.com" class="text-sm font-bold text-red-600 hover:underline">Otkaži pretplatu</a>
        </div>
    </div>
</div>
@endsection
