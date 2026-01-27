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
        
        // Status Logic Map
        // Visite -> visited
        // Relance -> follow_up
        // Vente -> purchased
        // Note: The progression visualizer uses these to determine active step
        
        $currentStatus = $client->status;
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
                    <div class="h-2 w-full {{ $client->status == 'purchased' ? 'bg-emerald-500' : ($client->status == 'follow_up' ? 'bg-[#E6AF5D]' : 'bg-neutral-900') }}"></div>
                    
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
                        
                        <!-- Client Type & Subcategory Display -->
                        <div class="mt-4 flex flex-col items-center gap-2">
                             @if($client->client_type === 'professionnel')
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 bg-black text-[#E6AF5D] text-[10px] font-bold uppercase tracking-wide rounded border border-[#E6AF5D]">PRO</span>
                                    @if($client->professional_category)
                                        <span class="px-2 py-1 bg-neutral-100 text-neutral-600 text-[10px] font-bold uppercase tracking-wide rounded border border-neutral-200">
                                            {{ $client->professional_category }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-neutral-400 italic">Catégorie non spécifiée</span>
                                    @endif
                                </div>
                            @else
                                <span class="px-3 py-1 bg-neutral-100 text-neutral-500 text-[10px] font-bold uppercase tracking-wide rounded">PARTICULIER</span>
                            @endif
                        </div>
                        
                        @if($client->potential_score)
                        <div class="mt-6 px-4">
                             <span class="block text-[10px] text-neutral-400 uppercase tracking-wider mb-1">Score Potentiel ({{ $client->potential_score }}%)</span>
                             <div class="w-full bg-neutral-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#E6AF5D] h-1.5 rounded-full transition-all duration-1000" style="width: {{ $client->potential_score }}%"></div>
                             </div>
                        </div>
                        @endif
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

                <!-- 2. CONTACT DETAILS -->
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
                
                <!-- 1. INTERACTIVE PIPELINE VISUALIZER -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200" x-data="{ currentStatus: '{{ $client->status }}' }">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Progression du dossier</h3>
                        <div class="text-xs font-bold text-neutral-900 bg-neutral-100 px-3 py-1 rounded-full uppercase" x-text="currentStatus === 'purchased' ? 'Client Actif' : (currentStatus === 'follow_up' ? 'À Relancer' : 'Visite / Prospect')"></div>
                    </div>
                    
                    <div class="relative flex items-center justify-between px-4">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-neutral-100 -z-0"></div>
                        
                        <!-- Hidden Form for Status Updates -->
                        <form id="statusForm" action="{{ route('clients.update', $client->id) }}" method="POST" class="hidden">
                             @csrf @method('PUT')
                             <input type="hidden" name="full_name" value="{{ $client->full_name }}">
                             <input type="hidden" name="client_type" value="{{ $client->client_type }}">
                             <input type="hidden" name="phone" value="{{ $client->phone }}">
                             <input type="hidden" name="status" id="statusInput">
                        </form>

                        <!-- Step 1: Visited -->
                        <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer group" @click="if(currentStatus !== 'visited') { document.getElementById('statusInput').value = 'visited'; document.getElementById('statusForm').submit(); }">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm ring-4 ring-white transition-all duration-300"
                                :class="currentStatus == 'visited' || currentStatus == 'follow_up' || currentStatus == 'purchased' ? 'bg-neutral-900 text-white' : 'bg-neutral-200 text-neutral-400'">
                                1
                            </div>
                            <span class="text-xs font-bold uppercase transition-colors" :class="currentStatus == 'visited' ? 'text-black' : 'text-neutral-400'">1er Contact</span>
                            <!-- Hover Tooltip -->
                            <span class="absolute -bottom-8 opacity-0 group-hover:opacity-100 bg-neutral-800 text-white text-[9px] px-2 py-1 rounded transition-opacity whitespace-nowrap">Marquer comme Visite</span>
                        </div>

                        <!-- Step 2: Follow Up -->
                        <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer group" @click="if(currentStatus !== 'follow_up') { document.getElementById('statusInput').value = 'follow_up'; document.getElementById('statusForm').submit(); }">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm ring-4 ring-white transition-all duration-300"
                                :class="currentStatus == 'follow_up' || currentStatus == 'purchased' ? 'bg-[#E6AF5D] text-white' : (currentStatus == 'visited' ? 'bg-neutral-200 text-neutral-400' : 'bg-neutral-200 text-neutral-400')">
                                2
                            </div>
                            <span class="text-xs font-bold uppercase transition-colors" :class="currentStatus == 'follow_up' ? 'text-[#E6AF5D]' : 'text-neutral-400'">À Relancer</span>
                             <span class="absolute -bottom-8 opacity-0 group-hover:opacity-100 bg-neutral-800 text-white text-[9px] px-2 py-1 rounded transition-opacity whitespace-nowrap">Marquer à Relancer</span>
                        </div>

                        <!-- Step 3: Purchased -->
                        <div class="relative z-10 flex flex-col items-center gap-2 cursor-pointer group" @click="if(currentStatus !== 'purchased') { document.getElementById('statusInput').value = 'purchased'; document.getElementById('statusForm').submit(); }">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-sm ring-4 ring-white transition-all duration-300"
                                :class="currentStatus == 'purchased' ? 'bg-emerald-500 text-white' : 'bg-neutral-200 text-neutral-400'">
                                3
                            </div>
                            <span class="text-xs font-bold uppercase transition-colors" :class="currentStatus == 'purchased' ? 'text-emerald-600' : 'text-neutral-400'">Vente Actée</span>
                             <span class="absolute -bottom-8 opacity-0 group-hover:opacity-100 bg-neutral-800 text-white text-[9px] px-2 py-1 rounded transition-opacity whitespace-nowrap">Marquer comme Vendu</span>
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
                                <span class="text-neutral-500">Status Intelligent</span>
                                <span class="font-bold text-neutral-900 uppercase text-xs">{{ $client->smart_status ?? '—' }}</span>
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

                <!-- 3. NOTES & REMARKS (Rich Editor Simulated) -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200" x-data="{ editing: false }">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <h3 class="text-xs font-bold text-neutral-900 uppercase tracking-widest">Remarques & Observations</h3>
                        </div>
                        <button @click="editing = !editing" x-show="!editing" class="text-xs font-bold text-neutral-500 hover:text-black">Modifier</button>
                         <button @click="editing = !editing" x-show="editing" class="text-xs font-bold text-red-500 hover:text-red-700">Annuler</button>
                    </div>
                
                    <div x-show="!editing">
                        @if($client->notes)
                            <div class="p-4 bg-[#FFFCF5] rounded-xl border border-[#E6AF5D]/20 text-sm text-neutral-800 leading-relaxed whitespace-pre-line font-medium min-h-[100px]">
                                {{ $client->notes }}
                            </div>
                        @else
                            <div class="p-4 bg-neutral-50 rounded-xl border border-neutral-100 text-sm text-neutral-400 italic min-h-[100px] flex items-center justify-center">
                                Aucune note enregistrée. Cliquez sur modifier.
                            </div>
                        @endif
                    </div>

                    <div x-show="editing" style="display: none;">
                        <form action="{{ route('clients.update', $client->id) }}" method="POST">
                            @csrf @method('PUT')
                             <input type="hidden" name="full_name" value="{{ $client->full_name }}">
                             <input type="hidden" name="client_type" value="{{ $client->client_type }}">
                             <input type="hidden" name="phone" value="{{ $client->phone }}">
                            
                            <!-- Simple Rich-Like Textarea -->
                            <div class="relative">
                                <textarea name="notes" rows="6" 
                                    class="w-full p-4 bg-white border border-neutral-300 rounded-xl focus:ring-2 focus:ring-[#E6AF5D] focus:border-[#E6AF5D] focus:outline-none transition-all text-sm leading-relaxed"
                                    placeholder="Saisissez vos remarques ici...">{{ $client->notes }}</textarea>
                                <div class="absolute bottom-3 right-3 text-[10px] text-neutral-400">Markdown supporté (bientôt)</div>
                            </div>
                            
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-neutral-900 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-black transition-colors">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection