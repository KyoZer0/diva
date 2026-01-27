@extends('layouts.app')

@section('title', 'Base Clients')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 bg-white rounded-[2rem] p-8 border border-neutral-100 shadow-sm relative overflow-hidden">
        <div class="absolute inset-0 bg-[#E6AF5D]/5 opacity-30"></div>
        
        <div class="relative z-10">
            <h1 class="text-4xl font-serif font-bold text-neutral-900 mb-2">Base Clients</h1>
            <p class="text-neutral-500 max-w-lg">
                Gérez l'intégralité de votre portefeuille client.
            </p>
        </div>

        <div class="relative z-10 flex gap-3">
            <a href="{{ route('clients.export') }}" class="px-5 py-3 bg-white border border-neutral-200 text-neutral-600 rounded-xl text-sm font-bold hover:bg-neutral-50 transition-colors shadow-sm">
                Exporter CSV
            </a>
            <a href="{{ route('clients.create') }}" class="px-5 py-3 bg-neutral-900 text-white rounded-xl text-sm font-bold hover:bg-black transition-colors shadow-lg shadow-neutral-200">
                + Nouveau Client
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-widest text-neutral-400 mb-2">Total</div>
            <div class="text-3xl font-serif font-bold text-neutral-900">{{ $stats->total }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-widest text-emerald-600 mb-2">Clients (Acheté)</div>
            <div class="text-3xl font-serif font-bold text-neutral-900">{{ $stats->purchased }}</div>
            <div class="text-xs text-emerald-600 mt-1 font-bold">+ Lead converti</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-widest text-[#E6AF5D] mb-2">En Cours</div>
            <div class="text-3xl font-serif font-bold text-neutral-900">{{ $stats->follow_up }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="text-[10px] font-bold uppercase tracking-widest text-blue-500 mb-2">Visites</div>
            <div class="text-3xl font-serif font-bold text-neutral-900">{{ $stats->visited }}</div>
        </div>
    </div>

    <!-- Filters & List -->
    <div class="bg-white rounded-[2rem] border border-neutral-100 shadow-sm overflow-hidden">
        
        <!-- Filter Bar -->
        <div class="p-6 border-b border-neutral-100 bg-neutral-50/50">
            <form method="GET" action="{{ route('clients.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un client, téléphone..." 
                           class="w-full bg-white border-neutral-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#E6AF5D] focus:border-transparent">
                </div>
                <div>
                     <select name="status" class="w-full bg-white border-neutral-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#E6AF5D] focus:border-transparent text-neutral-600">
                        <option value="">Tous les statuts</option>
                        <option value="visited" {{ request('status') === 'visited' ? 'selected' : '' }}>Visite</option>
                        <option value="follow_up" {{ request('status') === 'follow_up' ? 'selected' : '' }}>En cours</option>
                        <option value="purchased" {{ request('status') === 'purchased' ? 'selected' : '' }}>Client (Acheté)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-neutral-900 text-white font-bold py-2.5 rounded-xl hover:bg-black transition-colors">
                        Filtrer
                    </button>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-neutral-50 border-b border-neutral-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest text-neutral-500">Client</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest text-neutral-500">Type</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest text-neutral-500">Contact</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold uppercase tracking-widest text-neutral-500">Statut</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-neutral-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($clients as $client)
                    <tr class="hover:bg-neutral-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-neutral-900 text-[#E6AF5D] flex items-center justify-center font-serif font-bold text-sm">
                                    {{ substr($client->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <a href="{{ route('clients.show', $client) }}" class="block font-bold text-neutral-900 hover:text-[#E6AF5D] transition-colors">
                                        {{ $client->full_name }}
                                    </a>
                                    @if($client->professional_category)
                                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">{{ $client->professional_category }}</span>
                                    @elseif($client->company_name)
                                        <span class="text-[10px] text-neutral-400 font-bold uppercase tracking-wide">{{ $client->company_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($client->client_type === 'professionnel')
                                <div class="flex flex-col items-start gap-1">
                                    <span class="px-2 py-0.5 bg-black text-[#E6AF5D] text-[10px] font-bold uppercase tracking-wide rounded border border-[#E6AF5D]">PRO</span>
                                    @if($client->professional_category)
                                        <span class="text-[9px] font-bold text-neutral-500 uppercase tracking-wider">{{ $client->professional_category }}</span>
                                    @endif
                                </div>
                            @else
                                <span class="px-2 py-1 bg-neutral-100 text-neutral-500 text-[10px] font-bold uppercase tracking-wide rounded">PARTICULIER</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-neutral-700">{{ $client->phone }}</div>
                            <div class="text-xs text-neutral-400">{{ $client->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'visited' => 'bg-blue-50 text-blue-600',
                                    'follow_up' => 'bg-orange-50 text-orange-600',
                                    'purchased' => 'bg-emerald-50 text-emerald-600',
                                    'prospect' => 'bg-neutral-100 text-neutral-500',
                                ];
                                $statusLabels = [
                                    'visited' => 'Visite',
                                    'follow_up' => 'À Suivre',
                                    'purchased' => 'Client',
                                    'prospect' => 'Prospect',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses[$client->status] ?? 'bg-gray-100 text-gray-500' }}">
                                {{ $statusLabels[$client->status] ?? ucfirst($client->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-neutral-100 text-neutral-400 hover:bg-neutral-900 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-neutral-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <h3 class="text-neutral-900 font-bold text-lg mb-1">Aucun client trouvé</h3>
                                <p class="text-neutral-400 text-sm mb-4">Commencez par ajouter votre premier client.</p>
                                <a href="{{ route('clients.create') }}" class="px-4 py-2 bg-neutral-900 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors">
                                    Ajouter un client
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-6 border-t border-neutral-100">
            {{ $clients->withQueryString()->links() }} 
        </div>
    </div>
</div>
@endsection