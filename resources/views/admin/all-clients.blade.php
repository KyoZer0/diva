@extends('layouts.app')

@section('title', 'Tous les clients')
@section('page-title', 'Tous les clients')
@section('page-description', 'Liste de tous les clients de tous les conseillers')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.clients.export') }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-black text-black rounded-lg text-sm font-medium hover:bg-black hover:text-white transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exporter CSV
        </a>
    </div>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border-2 border-black p-4 rounded-xl shadow-sm">
            <div class="text-sm text-gray-600 mb-1">Total Clients</div>
            <div class="text-2xl font-bold text-black">{{ $clients->count() }}</div>
        </div>
        <div class="bg-amber-50 border-2 border-amber-300 p-4 rounded-xl shadow-sm">
            <div class="text-sm text-amber-700 mb-1">Ont acheté</div>
            <div class="text-2xl font-bold text-amber-900">{{ $clients->where('status', 'purchased')->count() }}</div>
        </div>
        <div class="bg-white border-2 border-gray-300 p-4 rounded-xl shadow-sm">
            <div class="text-sm text-gray-600 mb-1">À recontacter</div>
            <div class="text-2xl font-bold text-gray-900">{{ $clients->where('status', 'follow_up')->count() }}</div>
        </div>
        <div class="bg-white border-2 border-gray-300 p-4 rounded-xl shadow-sm">
            <div class="text-sm text-gray-600 mb-1">Visités</div>
            <div class="text-2xl font-bold text-gray-900">{{ $clients->where('status', 'visited')->count() }}</div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('admin.clients') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all text-sm">
                </div>
                
                <!-- Rep Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Conseiller</label>
                    <select name="rep_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all text-sm">
                        <option value="">Tous les conseillers</option>
                        @foreach($reps as $rep)
                            <option value="{{ $rep->id }}" {{ request('rep_id') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all text-sm">
                        <option value="">Tous les statuts</option>
                        <option value="visited" {{ request('status') === 'visited' ? 'selected' : '' }}>A visité</option>
                        <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>À recontacter</option>
                        <option value="purchased" {{ request('status') === 'purchased' ? 'selected' : '' }}>Ont acheté</option>
                        <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                
                <!-- Client Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de client</label>
                    <select name="client_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all text-sm">
                        <option value="">Tous les types</option>
                        <option value="particulier" {{ request('client_type') === 'particulier' ? 'selected' : '' }}>Particulier</option>
                        <option value="professionnel" {{ request('client_type') === 'professionnel' ? 'selected' : '' }}>Professionnel</option>
                    </select>
                </div>
                
                <!-- City Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
                    <select name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all text-sm">
                        <option value="">Toutes les villes</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-amber-500 text-black rounded-lg font-medium hover:bg-amber-600 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'status', 'client_type', 'city', 'source', 'rep_id']))
                    <a href="{{ route('admin.clients') }}" class="inline-flex items-center px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Réinitialiser
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Conseiller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date ajout</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-black rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($client->full_name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('clients.show', $client) }}" class="text-sm font-medium text-gray-900 hover:text-amber-600 transition-colors">
                                            {{ $client->full_name }}
                                        </a>
                                        @if($client->company_name)
                                            <div class="text-xs text-gray-500">{{ $client->company_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->client_type === 'particulier' ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $client->phone }}</div>
                                @if($client->email)
                                    <div class="text-xs text-gray-500">{{ $client->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $client->city ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $client->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $client->status === 'purchased' ? 'bg-green-100 text-green-800' : 
                                       ($client->status === 'follow_up' ? 'bg-orange-100 text-orange-800' : 
                                       ($client->status === 'visited' ? 'bg-blue-100 text-blue-800' : 
                                       ($client->status === 'prospect' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                    @if($client->status === 'visited')
                                        A visité
                                    @elseif($client->status === 'purchased')
                                        Client
                                    @elseif($client->status === 'follow_up')
                                        À recontacter
                                    @elseif($client->status === 'prospect')
                                        Prospect
                                    @else
                                        {{ ucfirst($client->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $client->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-900 rounded-lg hover:bg-amber-100 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
                                <p class="mt-1 text-sm text-gray-500">Aucun client ne correspond à vos critères de recherche.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

