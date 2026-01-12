@extends('layouts.app')

@section('title', 'Gestion Clients')
@section('page-title', 'Base de Données Clients')

@section('content')

    <!-- TOP BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Liste des Clients</h1>
            <p class="text-neutral-500 text-sm mt-1">Gérez l'ensemble du portefeuille client.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.clients.export') }}" class="group inline-flex items-center px-5 py-2.5 bg-white border border-neutral-200 text-neutral-700 rounded-xl text-sm font-bold hover:border-[#E6AF5D] hover:text-[#E6AF5D] transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2 text-neutral-400 group-hover:text-[#E6AF5D] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exporter Excel
            </a>
            <a href="{{ route('clients.create') }}" class="inline-flex items-center px-5 py-2.5 bg-black text-white rounded-xl text-sm font-bold hover:bg-neutral-800 transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau Client
            </a>
        </div>
    </div>

    <!-- 1. KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <!-- Total -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm group hover:border-black/30 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Total Dossiers</span>
                <div class="p-1.5 bg-neutral-900 rounded-lg text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-neutral-900">{{ $clients->count() }}</h3>
        </div>

        <!-- Purchased -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm group hover:border-emerald-400 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Clients Actifs</span>
                <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-neutral-900">{{ $clients->where('status', 'purchased')->count() }}</h3>
        </div>

        <!-- Follow Up -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm group hover:border-[#E6AF5D] transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">À Relancer</span>
                <div class="p-1.5 bg-[#FFFBEB] text-[#D97706] rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-neutral-900">{{ $clients->where('status', 'follow_up')->count() }}</h3>
        </div>

        <!-- Visited -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm group hover:border-blue-400 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Visites Simples</span>
                <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-neutral-900">{{ $clients->where('status', 'visited')->count() }}</h3>
        </div>
    </div>

    <!-- 2. FILTER BAR -->
    <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.clients') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Search -->
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-[#E6AF5D] focus:border-[#E6AF5D] transition-colors placeholder-neutral-400">
                </div>
                
                <!-- Rep Filter -->
                <select name="rep_id" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-[#E6AF5D] focus:border-[#E6AF5D] text-neutral-700">
                    <option value="">Tous les conseillers</option>
                    @foreach($reps as $rep)
                        <option value="{{ $rep->id }}" {{ request('rep_id') == $rep->id ? 'selected' : '' }}>{{ $rep->name }}</option>
                    @endforeach
                </select>
                
                <!-- Status Filter -->
                <select name="status" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-[#E6AF5D] focus:border-[#E6AF5D] text-neutral-700">
                    <option value="">Tous les statuts</option>
                    <option value="visited" {{ request('status') === 'visited' ? 'selected' : '' }}>A visité</option>
                    <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>À recontacter</option>
                    <option value="purchased" {{ request('status') === 'purchased' ? 'selected' : '' }}>Ont acheté</option>
                    <option value="prospect" {{ request('status') === 'prospect' ? 'selected' : '' }}>Prospect</option>
                </select>
                
                <!-- City Filter -->
                <select name="city" class="w-full px-4 py-2.5 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:ring-[#E6AF5D] focus:border-[#E6AF5D] text-neutral-700">
                    <option value="">Toutes les villes</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-[#E6AF5D] hover:bg-[#d9a04a] text-white rounded-xl px-4 py-2.5 text-sm font-bold transition-all shadow-sm">
                        Filtrer
                    </button>
                    @if(request()->hasAny(['search', 'status', 'client_type', 'city', 'rep_id']))
                        <a href="{{ route('admin.clients') }}" class="flex items-center justify-center w-10 h-full bg-neutral-100 hover:bg-neutral-200 text-neutral-600 rounded-xl transition-all" title="Réinitialiser">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- 3. CLIENTS TABLE -->
    <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-100">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Ville</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Conseiller</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-neutral-400 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-neutral-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 bg-white">
                    @forelse($clients as $client)
                        <tr class="hover:bg-neutral-50/80 transition-colors group">
                            <!-- Client Name & Avatar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-neutral-900 rounded-full flex items-center justify-center text-[#E6AF5D] font-bold text-sm shadow-sm ring-2 ring-white">
                                        {{ strtoupper(substr($client->full_name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('clients.show', $client) }}" class="text-sm font-bold text-neutral-900 hover:text-[#E6AF5D] transition-colors">
                                            {{ $client->full_name }}
                                        </a>
                                        @if($client->company_name)
                                            <div class="flex items-center text-xs text-neutral-400 mt-0.5">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                {{ $client->company_name }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->client_type === 'particulier')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-neutral-100 text-neutral-600 border border-neutral-200">
                                        Particulier
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-neutral-900 text-white border border-neutral-900">
                                        Pro
                                    </span>
                                @endif
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-neutral-900">{{ $client->phone }}</div>
                                @if($client->email)
                                    <div class="text-xs text-neutral-400 truncate max-w-[150px]">{{ $client->email }}</div>
                                @endif
                            </td>

                            <!-- Ville -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-neutral-700">
                                {{ $client->city ?? '—' }}
                            </td>

                            <!-- Conseiller -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-6 w-6 bg-neutral-100 rounded-full flex items-center justify-center text-neutral-500 text-[10px] font-bold border border-neutral-200">
                                        {{ $client->user ? substr($client->user->name, 0, 1) : '?' }}
                                    </div>
                                    <span class="ml-2 text-sm text-neutral-600">{{ $client->user->name ?? 'Non assigné' }}</span>
                                </div>
                            </td>

                            <!-- Statut -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'purchased' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'label' => 'Client Actif'],
                                        'follow_up' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'label' => 'À Relancer'],
                                        'visited' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'label' => 'A Visité'],
                                    ];
                                    $config = $statusConfig[$client->status] ?? ['bg' => 'bg-neutral-50', 'text' => 'text-neutral-600', 'border' => 'border-neutral-200', 'label' => ucfirst($client->status)];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }} border {{ $config['border'] }}">
                                    {{ $config['label'] }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- View -->
                                    <a href="{{ route('clients.show', $client) }}" class="p-2 text-neutral-400 hover:text-black hover:bg-neutral-100 rounded-lg transition-colors" title="Voir">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    
                                    <!-- Edit -->
                                    <a href="{{ route('clients.edit', $client) }}" class="p-2 text-neutral-400 hover:text-[#E6AF5D] hover:bg-[#FFFBEB] rounded-lg transition-colors" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    
                                    <!-- Delete -->
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce client définitivement ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-neutral-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-neutral-50 mb-4">
                                    <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-medium text-neutral-900">Aucun résultat</h3>
                                <p class="text-neutral-500 mt-1">Essayez de modifier vos filtres de recherche.</p>
                                <a href="{{ route('admin.clients') }}" class="inline-block mt-4 text-[#E6AF5D] font-bold hover:underline">Tout effacer</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection