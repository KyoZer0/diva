@extends('layouts.app')

@section('title', 'Détails du client')
@section('page-title', $client->full_name)
@section('page-description', 'Informations, préférences et historique')

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-center justify-between bg-white rounded-2xl shadow-sm border border-neutral-200 p-8 mb-8">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-300 rounded-full flex items-center justify-center text-3xl font-semibold text-blue-700 shadow-inner">
                {{ strtoupper(substr($client->full_name, 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral-900 mb-1">{{ $client->full_name }}</h1>
                <p class="text-sm text-neutral-600 flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $client->client_type === 'particulier' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel' }}
                    </span>
                    @if($client->status)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @switch($client->status)
                                @case('purchased') bg-green-100 text-green-800 @break
                                @case('follow_up') bg-orange-100 text-orange-800 @break
                                @default bg-neutral-100 text-neutral-700
                            @endswitch">
                            {{ ucfirst($client->status) }}
                        </span>
                    @endif
                </p>
            </div>
        </div>

    @if(auth()->user()->is_admin ?? false)
        <div class="mt-6 md:mt-0">
            <a href="{{ route('clients.edit', $client->id) }}"
               class="inline-flex items-center px-5 py-2.5 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all text-sm font-medium shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3zM3 21h18"/>
                </svg>
                Modifier
            </a>
        </div>
    @endif
</div>

<!-- Info Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
    <!-- Contact Info -->
    <div class="bg-white rounded-2xl border border-neutral-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/>
            </svg>
            Coordonnées
        </h3>
        <div class="space-y-3 text-sm">
            <p><strong>Téléphone :</strong> {{ $client->phone }}</p>
            @if($client->email) <p><strong>Email :</strong> {{ $client->email }}</p> @endif
            @if($client->city) <p><strong>Ville :</strong> {{ $client->city }}</p> @endif
            @if($client->company_name) <p><strong>Entreprise :</strong> {{ $client->company_name }}</p> @endif
        </div>
    </div>

    <!-- Business Details -->
    <div class="bg-white rounded-2xl border border-neutral-200 p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745"/>
            </svg>
            Détails commerciaux
        </h3>
        <div class="space-y-3 text-sm">
            @if($client->source)
                <p><strong>Source :</strong> {{ ucfirst(str_replace('_', ' ', $client->source)) }}</p>
            @endif
            @if($client->conseiller)
                <p><strong>Conseiller :</strong> {{ $client->conseiller }}</p>
            @endif
            <p><strong>Devis demandé :</strong>
                {!! $client->devis_demande
                    ? '<span class="text-green-600 font-medium">Oui</span>'
                    : '<span class="text-neutral-500">Non</span>' !!}
            </p>
        </div>
    </div>
</div>

<!-- Produits d'intérêt -->
@if($client->products)
    <div class="bg-white rounded-2xl border border-neutral-200 p-6 shadow-sm mb-10">
        <h3 class="text-lg font-semibold text-neutral-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4M4 7v10l8 4"/>
            </svg>
            Produits d’intérêt
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($client->products as $product)
                <span class="px-4 py-1.5 bg-green-50 text-green-700 rounded-full text-sm font-medium">
                    {{ ucfirst($product) }}
                </span>
            @endforeach
        </div>
    </div>
@endif

<!-- Notes -->
@if($client->notes)
    <div class="bg-white rounded-2xl border border-neutral-200 p-6 shadow-sm mb-10">
        <h3 class="text-lg font-semibold text-neutral-900 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            Remarques
        </h3>
        <div class="p-4 bg-orange-50 rounded-xl border-l-4 border-orange-400 text-neutral-700 whitespace-pre-line">
            {{ $client->notes }}
        </div>
    </div>
@endif

<!-- Timestamps -->
<div class="text-xs text-neutral-500 text-right">
    Créé le {{ $client->created_at->format('d/m/Y à H:i') }}
    @if($client->updated_at->ne($client->created_at))
        • Mis à jour le {{ $client->updated_at->format('d/m/Y à H:i') }}
    @endif
</div>
```

</div>

<style>
    .bg-white {
        animation: fadeIn 0.5s ease forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@endsection
