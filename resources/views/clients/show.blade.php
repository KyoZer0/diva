@extends('layouts.app')

@section('title', 'Dossier Client')
@section('page-title', $client->full_name)

@section('content')

    {{-- Data Processing --}}
    @php
        // Decode JSON fields safely
        $products = is_string($client->products) ? json_decode($client->products, true) : $client->products;
        if (!is_array($products)) $products = [];

        $styles = is_string($client->style) ? json_decode($client->style, true) : $client->style;
        if (!is_array($styles)) $styles = [];
        
        // Status Logic
        $isSold = $client->status === 'purchased';
        $statusLabel = match($client->status) {
            'purchased' => 'Client Actif',
            'follow_up' => 'À Relancer',
            default => 'Prospect / Visite'
        };
        
        // Visual Progress Logic (0 = Visited, 1 = Quote, 2 = Sold)
        $progressStep = 0;
        if($client->devis_demande) $progressStep = 1;
        if($isSold) $progressStep = 2;
    @endphp

    <div class="max-w-7xl mx-auto mb-12">
        
        <!-- TOP NAVIGATION BAR -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center text-sm font-bold text-neutral-500 hover:text-black transition-colors group">
                <div class="w-8 h-8 rounded-full bg-white border border-neutral-200 flex items-center justify-center mr-2 group-hover:border-black transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </div>
                Retour liste
            </a>
            
            <div class="flex items-center gap-2">
                @if(Auth::user()->isAdmin())
                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" onsubmit="return confirm('Supprimer ce dossier ?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                            Supprimer
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('clients.edit', $client->id) }}" class="inline-flex items-center px-5 py-2 bg-white border border-neutral-200 text-neutral-900 text-sm font-bold rounded-xl hover:border-black transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Modifier
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COLUMN: IDENTITY & ACTIONS -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- 1. IDENTITY CARD -->
                <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm overflow-hidden relative">
                    <!-- Status Banner -->
                    <div class="h-2 w-full {{ $isSold ? 'bg-emerald-500' : ($client->status == 'follow_up' ? 'bg-[#E6AF5D]' : 'bg-neutral-900') }}"></div>
                    
                    <div class="p-6 text-center">
                        <div class="w-24 h-24 mx-auto rounded-full bg-neutral-900 text-[#E6AF5D] flex items-center justify-center text-3xl font-bold border-4 border-white shadow-lg mb-4">
                            {{ strtoupper(substr($client->full_name, 0, 2)) }}
                        </div>
                        
                        <h1 class="text-xl font-bold text-neutral-900">{{ $client->full_name }}</h1>
                        @if($client->company_name)
                            <p class="text-sm font-medium text-neutral-500 mt-1 flex items-center justify-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $client->company_name }}
                            </p>
                        @endif

                        <div class="mt-4 flex justify-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                {{ $isSold ? 'bg-emerald-100 text-emerald-700' : ($client->status == 'follow_up' ? 'bg-[#FFFBEB] text-amber-700' : 'bg-neutral-100 text-neutral-600') }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <!-- Communication Grid -->
                    <div class="grid grid-cols-3 border-t border-neutral-100 divide-x divide-neutral-100">
                        <a href="tel:{{ $client->phone }}" class="p-4 flex flex-col items-center hover:bg-neutral-50 transition-colors group">
                            <svg class="w-6 h-6 text-neutral-400 group-hover:text-black mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="text-[10px] font-bold text-neutral-500 uppercase">Appeler</span>
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->phone) }}" target="_blank" class="p-4 flex flex-col items-center hover:bg-emerald-50 transition-colors group">
                            <svg class="w-6 h-6 text-neutral-400 group-hover:text-emerald-500 mb-1" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            <span class="text-[10px] font-bold text-neutral-500 uppercase">WhatsApp</span>
                        </a>
                        <a href="mailto:{{ $client->email }}" class="p-4 flex flex-col items-center hover:bg-neutral-50 transition-colors group">
                            <svg class="w-6 h-6 text-neutral-400 group-hover:text-black mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] font-bold text-neutral-500 uppercase">Email</span>
                        </a>
                    </div>
                </div>

                <!-- 2. QUICK CLOSE ACTION (Featured) -->
                @if(!$isSold)
                <div class="bg-gradient-to-br from-white to-neutral-50 p-6 rounded-2xl border border-neutral-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-3 opacity-5">
                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    </div>
                    
                    <h3 class="text-sm font-bold text-neutral-900 uppercase tracking-wider mb-1">Action Rapide</h3>
                    <p class="text-xs text-neutral-500 mb-4">Conclure ce dossier maintenant ?</p>
                    
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="full_name" value="{{ $client->full_name }}">
                        <input type="hidden" name="client_type" value="{{ $client->client_type }}">
                        <input type="hidden" name="phone" value="{{ $client->phone }}">
                        <input type="hidden" name="status" value="purchased">
                        <input type="hidden" name="last_contact_date" value="{{ now()->format('Y-m-d') }}">
                        
                        <button type="submit" class="w-full py-3 bg-neutral-900 hover:bg-black text-white text-sm font-bold rounded-xl shadow-lg shadow-neutral-200 transition-all flex items-center justify-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Marquer comme Vendu
                        </button>
                    </form>
                </div>
                @else
                <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white text-emerald-500 flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-emerald-900 font-bold">Dossier Clos</h3>
                        <p class="text-emerald-700 text-xs mt-0.5">Vente enregistrée avec succès.</p>
                    </div>
                </div>
                @endif

                <!-- 3. CONTACT DETAILS -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200">
                    <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-4">Coordonnées</h3>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-neutral-900">{{ $client->phone }}</span>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-sm font-medium text-neutral-600 truncate">{{ $client->email ?? 'Non renseigné' }}</span>
                        </div>

                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($client->city) }}" target="_blank" class="flex items-center gap-3 group">
                            <div class="w-8 h-8 rounded-lg bg-neutral-50 flex items-center justify-center text-neutral-400 group-hover:bg-[#FFFBEB] group-hover:text-[#E6AF5D] transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-sm font-medium text-neutral-600 group-hover:text-black group-hover:underline decoration-[#E6AF5D] decoration-2 underline-offset-2 transition-all">
                                {{ $client->city ?? 'Non renseigné' }}
                            </span>
                        </a>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: CONTEXT & DETAILS -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. PIPELINE VISUALIZER (New Feature) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Progression du dossier</h3>
                        <span class="text-xs font-bold text-neutral-900 bg-neutral-100 px-2 py-1 rounded">Étape {{ $progressStep + 1 }}/3</span>
                    </div>
                    
                    <div class="relative flex items-center justify-between">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-neutral-100 -z-0"></div>
                        
                        <!-- Step 1 -->
                        <div class="relative z-10 flex flex-col items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $progressStep >= 0 ? 'bg-neutral-900 text-white' : 'bg-neutral-200 text-neutral-400' }} flex items-center justify-center font-bold text-xs shadow-sm ring-4 ring-white">
                                1
                            </div>
                            <span class="text-xs font-bold {{ $progressStep >= 0 ? 'text-neutral-900' : 'text-neutral-400' }}">Contact</span>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative z-10 flex flex-col items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $progressStep >= 1 ? 'bg-[#E6AF5D] text-white' : 'bg-neutral-200 text-neutral-400' }} flex items-center justify-center font-bold text-xs shadow-sm ring-4 ring-white transition-colors duration-500">
                                2
                            </div>
                            <span class="text-xs font-bold {{ $progressStep >= 1 ? 'text-neutral-900' : 'text-neutral-400' }}">Devis</span>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative z-10 flex flex-col items-center gap-2">
                            <div class="w-8 h-8 rounded-full {{ $progressStep >= 2 ? 'bg-emerald-500 text-white' : 'bg-neutral-200 text-neutral-400' }} flex items-center justify-center font-bold text-xs shadow-sm ring-4 ring-white transition-colors duration-500">
                                3
                            </div>
                            <span class="text-xs font-bold {{ $progressStep >= 2 ? 'text-neutral-900' : 'text-neutral-400' }}">Vente</span>
                        </div>
                    </div>
                </div>

                <!-- 2. DETAILS GRID -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Preferences -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200">
                        <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-4">Intérêts</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="block text-[10px] font-bold text-neutral-400 mb-2">PRODUITS</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($products as $p)
                                        <span class="px-2.5 py-1 bg-neutral-50 border border-neutral-200 rounded-lg text-xs font-medium text-neutral-700">
                                            {{ ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $p)) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-neutral-400 italic">Aucun</span>
                                    @endforelse
                                </div>
                            </div>
                            
                            <div>
                                <span class="block text-[10px] font-bold text-neutral-400 mb-2">STYLE</span>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($styles as $s)
                                        <span class="px-2.5 py-1 bg-[#FFFBEB] border border-[#E6AF5D]/30 rounded-lg text-xs font-medium text-amber-800">
                                            {{ ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $s)) }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-neutral-400 italic">Aucun</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200">
                        <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-widest mb-4">Contexte</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex justify-between">
                                <span class="text-neutral-500">Source</span>
                                <span class="font-bold text-neutral-900">{{ $client->source ? ucfirst(str_replace('_', ' ', $client->source)) : '—' }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-neutral-500">Conseiller</span>
                                <span class="font-bold text-neutral-900">{{ $client->conseiller ?? '—' }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-neutral-500">Création</span>
                                <span class="font-medium text-neutral-700">{{ $client->created_at->format('d/m/Y') }}</span>
                            </li>
                            <li class="flex justify-between">
                                <span class="text-neutral-500">Dernier contact</span>
                                <span class="font-medium text-neutral-700">{{ $client->last_contact_date ? $client->last_contact_date->format('d/m/Y') : '—' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- 3. NOTES -->
                <div class="bg-[#FFFCF5] p-6 rounded-2xl border border-[#E6AF5D]/30 relative">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-4 h-4 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <h3 class="text-xs font-bold text-amber-900 uppercase tracking-widest">Remarques & Observations</h3>
                    </div>
                    
                    @if($client->notes)
                        <div class="text-sm text-neutral-700 leading-relaxed whitespace-pre-line font-medium">
                            {{ $client->notes }}
                        </div>
                    @else
                        <p class="text-sm text-neutral-400 italic">Aucune note spécifique pour ce client.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection