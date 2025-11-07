@extends('layouts.app')

@section('title', 'Détails du client')
@section('page-title', $client->full_name)
@section('page-description', 'Informations du client')

@section('content')

<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('clients.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-medium">Retour à mes clients</span>
        </a>
    </div>

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-black rounded-lg flex items-center justify-center text-2xl font-bold text-white">
                    {{ strtoupper(substr($client->full_name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-black">{{ $client->full_name }}</h1>
                    @if($client->company_name)
                        <p class="text-gray-600 mt-1">{{ $client->company_name }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                            {{ $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel' }}
                        </span>
                        @if($client->status)
                            <span class="px-2 py-1 bg-amber-50 text-amber-700 text-xs font-medium rounded">
                                @if($client->status === 'visited') A visité
                                @elseif($client->status === 'purchased') Client
                                @elseif($client->status === 'follow_up') À recontacter
                                @else {{ ucfirst($client->status) }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex gap-2">
                <a href="tel:{{ preg_replace('/\s+/', '', $client->phone) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:border-black transition-all text-sm font-medium">
                    Appeler
                </a>
                @if($client->email)
                    <a href="mailto:{{ $client->email }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:border-black transition-all text-sm font-medium">
                        Email
                    </a>
                @endif
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->phone) }}" target="_blank" class="px-4 py-2 bg-amber-500 text-black rounded-lg hover:bg-amber-600 transition-all text-sm font-medium">
                    WhatsApp
                </a>
            </div>
        </div>
    </div>

    <!-- Client Information Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Contact Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Informations de contact</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs text-gray-500 font-medium">Téléphone</label>
                    <p class="text-gray-900 font-medium">{{ $client->phone }}</p>
                </div>
                @if($client->email)
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Email</label>
                        <p class="text-gray-900 font-medium break-all">{{ $client->email }}</p>
                    </div>
                @endif
                @if($client->city)
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Ville</label>
                        <p class="text-gray-900 font-medium">{{ $client->city }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Business Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-bold text-gray-900 mb-4">Informations commerciales</h3>
            <div class="space-y-3">
                @if($client->source)
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Source</label>
                        <p class="text-gray-900 font-medium">{{ ucfirst(str_replace('_', ' ', $client->source)) }}</p>
                    </div>
                @endif
                @if($client->conseiller)
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Conseiller</label>
                        <p class="text-gray-900 font-medium">{{ $client->conseiller }}</p>
                    </div>
                @endif
                <div>
                    <label class="text-xs text-gray-500 font-medium">Devis demandé</label>
                    <p class="text-gray-900 font-medium">
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $client->devis_demande ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $client->devis_demande ? 'Oui' : 'Non' }}
                        </span>
                    </p>
                </div>
                @if($client->last_contact_date)
                    <div>
                        <label class="text-xs text-gray-500 font-medium">Dernier contact</label>
                        <p class="text-gray-900 font-medium">{{ $client->last_contact_date->format('d/m/Y') }} ({{ $client->last_contact_date->diffForHumans() }})</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Products of Interest -->
    @if($client->products && count($client->products) > 0)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
            <h3 class="font-bold text-gray-900 mb-4">Produits d'intérêt</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($client->products as $product)
                    <span class="px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg text-sm font-medium">
                        {{ ucfirst($product) }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Notes -->
    @if($client->notes)
        <div class="bg-white rounded-xl border border-gray-200 p-6 mt-6">
            <h3 class="font-bold text-gray-900 mb-4">Notes</h3>
            <div class="p-4 bg-gray-50 rounded-lg text-gray-700 whitespace-pre-line">{{ $client->notes }}</div>
        </div>
    @endif

    <!-- Meta Information -->
    <div class="mt-6 text-xs text-gray-500 text-center">
        Ajouté le {{ $client->created_at->format('d/m/Y à H:i') }} par {{ $client->user->name }}
        @if($client->updated_at->ne($client->created_at))
            • Modifié le {{ $client->updated_at->format('d/m/Y à H:i') }}
        @endif
    </div>
</div>

@endsection
