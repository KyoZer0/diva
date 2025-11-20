@extends('layouts.app')

@section('title', 'Dossier Client')
@section('page-title', $client->full_name)

@section('content')

<!-- Main Wrapper: Subtle dotted pattern for texture without noise -->
<div class="min-h-screen bg-slate-50 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px] py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 px-1">
                <li>
                    <a href="{{ route('clients.index') }}" class="group flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:text-indigo-600 hover:border-indigo-200 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                </li>
                <li><span class="text-slate-300">/</span></li>
                <li><a href="{{ route('clients.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">Clients</a></li>
                <li><span class="text-slate-300">/</span></li>
                <li><span class="text-sm font-medium text-slate-900">Dossier #{{ $client->id }}</span></li>
            </ol>
        </nav>

        <!-- GRID LAYOUT -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 items-start">

            <!-- LEFT COLUMN (Main Profile & Actions) - Spans 2 columns -->
            <div class="xl:col-span-2 space-y-6">
                
                <!-- 1. HERO CARD -->
                <div class="bg-white rounded-3xl p-8 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-slate-100 relative overflow-hidden group">
                    <!-- Subtle gradient glow top right -->
                    <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col md:flex-row md:items-start gap-6">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-24 h-24 rounded-[2rem] bg-slate-900 text-white flex items-center justify-center text-3xl font-bold shadow-xl shadow-slate-200 ring-8 ring-slate-50/50">
                                {{ strtoupper(substr($client->full_name, 0, 2)) }}
                            </div>
                        </div>
                        
                        <!-- Identity -->
                        <div class="flex-1 pt-1">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight leading-tight">{{ $client->full_name }}</h1>
                                    @if($client->company_name)
                                        <p class="text-lg text-slate-500 font-medium mt-1 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            {{ $client->company_name }}
                                        </p>
                                    @endif
                                </div>
                                
                                <!-- Status Pills -->
                                <div class="flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        {{ $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel' }}
                                    </span>

                                    @if($client->status)
                                        @php
                                            $statusConfig = [
                                                'visited' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500', 'label' => 'Visite effectuée'],
                                                'purchased' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500', 'label' => 'Client Actif'],
                                                'follow_up' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'dot' => 'bg-amber-500', 'label' => 'À Relancer'],
                                            ];
                                            $config = $statusConfig[$client->status] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'dot' => 'bg-slate-500', 'label' => ucfirst($client->status)];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }} border border-transparent">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }} animate-pulse"></span>
                                            {{ $config['label'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Bar -->
                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="tel:{{ preg_replace('/\s+/', '', $client->phone) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-slate-800 hover:shadow-lg hover:shadow-slate-900/20 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    Appeler
                                </a>
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $client->phone) }}" target="_blank" class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                    WhatsApp
                                </a>
                                @if($client->email)
                                <a href="mailto:{{ $client->email }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-medium rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Email
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. PRIMARY DETAILS (Split Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Contact Info -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:border-slate-300 transition-colors">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Coordonnées
                        </h3>
                        
                        <div class="space-y-5">
                            <div class="group">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Téléphone</label>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-base font-medium text-slate-900 font-mono tracking-tight select-all">{{ $client->phone }}</p>
                                    <button class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-blue-600 transition-all" title="Copier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="group">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</label>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-base font-medium text-slate-900 truncate select-all">{{ $client->email ?? '—' }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Localisation</label>
                                <div class="mt-2 flex items-center gap-3 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <div class="w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-slate-700">{{ $client->city ?? 'Ville inconnue' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commercial Info -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60 hover:border-slate-300 transition-colors">
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-violet-500"></span> Dossier
                        </h3>

                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Source</label>
                                    <p class="mt-1 text-sm font-semibold text-slate-700">{{ $client->source ? ucfirst(str_replace('_', ' ', $client->source)) : '—' }}</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Devis</label>
                                    <div class="mt-1">
                                        @if($client->devis_demande)
                                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">Demandé</span>
                                        @else
                                            <span class="text-xs font-medium text-slate-400">Aucun</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Conseiller</label>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-violet-50 border border-violet-100 flex items-center justify-center text-violet-600 text-xs font-bold">
                                        {{ $client->conseiller ? substr($client->conseiller, 0, 1) : '?' }}
                                    </div>
                                    <p class="text-sm font-medium text-slate-900">{{ $client->conseiller ?? 'Non assigné' }}</p>
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-50">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-medium">Dernier contact</span>
                                    <span class="text-slate-700 font-bold bg-slate-100 px-2 py-1 rounded-lg">
                                        {{ $client->last_contact_date ? $client->last_contact_date->format('d/m/Y') : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (Context & Metadata) -->
            <div class="space-y-6">
                
                <!-- Preferences Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/60">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Intérêts
                    </h3>

                    <div class="space-y-6">
                        @php
                            $products = is_array($client->products) ? $client->products : [];
                            $style = is_array($client->style) ? $client->style : [];
                        @endphp

                        @if(count($products) > 0)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Produits</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($products as $p)
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 transition-colors cursor-default">
                                        {{ ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $p)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($style) > 0)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 block">Style</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($style as $s)
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 hover:border-rose-200 transition-colors cursor-default">
                                        {{ ucfirst(str_replace(['_', 'Autres: '], [' ', ''], $s)) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($products) === 0 && count($style) === 0)
                            <div class="text-center py-8 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                <span class="text-slate-400 text-sm">Aucune préférence enregistrée</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes Sticky Note -->
                <div class="bg-[#FFFBEB] rounded-3xl p-6 border border-[#FDE68A] relative overflow-hidden">
                    <!-- Decorative tape effect -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-16 h-4 bg-[#FCD34D]/20 blur-sm rounded-b-lg"></div>
                    
                    <h3 class="text-sm font-bold text-amber-900/70 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Notes Internes
                    </h3>
                    
                    @if($client->notes)
                        <div class="prose prose-sm prose-amber max-w-none">
                            <p class="text-amber-900 text-sm leading-relaxed whitespace-pre-line font-medium">{{ $client->notes }}</p>
                        </div>
                    @else
                        <p class="text-amber-900/40 text-sm italic">Aucune note pour ce client.</p>
                    @endif
                </div>

                <!-- Meta Info -->
                <div class="px-4 text-center">
                    <p class="text-[10px] font-semibold text-slate-300 uppercase tracking-widest">
                        Créé le {{ $client->created_at->format('d/m/Y') }} <br>
                        par {{ $client->user->name ?? 'Admin' }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection