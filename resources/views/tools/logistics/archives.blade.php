@extends('layouts.app')

@section('title', 'Registre Journalier')
@section('content')

<!-- Typography -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    .font-serif { font-family: 'Playfair Display', serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }
    
    /* Ledger Row Styling */
    .ledger-row {
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border-bottom: 1px solid #F3F4F6;
        position: relative;
    }
    .ledger-row:hover {
        background-color: #FAFAFA;
        transform: scale(1.01);
        z-index: 10;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        border-bottom-color: transparent;
    }
    .ledger-row::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: #E6AF5D;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .ledger-row:hover::before { opacity: 1; }

    /* Custom Date Input styling */
    .date-trigger::-webkit-calendar-picker-indicator {
        position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    }
</style>

<div class="max-w-6xl mx-auto pb-20 pt-8">

    <!-- 1. NAVIGATION & HERO -->
    <div class="flex flex-col md:flex-row items-center justify-between mb-16 gap-8">
        
        <!-- Date Switcher -->
        <div class="flex items-center gap-6">
            <a href="?date={{ \Carbon\Carbon::parse($date)->subDay()->format('Y-m-d') }}" class="w-12 h-12 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-400 hover:border-black hover:text-black transition-all group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/></svg>
            </a>

            <div class="text-center relative group">
                <span class="text-[10px] font-bold text-[#E6AF5D] uppercase tracking-[0.3em] mb-1 block">Registre</span>
                <h1 class="text-4xl md:text-5xl font-serif text-neutral-900 leading-tight">
                    {{ \Carbon\Carbon::parse($date)->locale('fr')->isoFormat('D MMMM') }}
                    <span class="text-neutral-300 font-sans font-light text-2xl">{{ \Carbon\Carbon::parse($date)->format('Y') }}</span>
                </h1>
                <!-- Hidden Input overlay -->
                <input type="date" value="{{ $date }}" class="date-trigger" onchange="window.location.href='?date='+this.value">
                <div class="h-px w-0 bg-black mx-auto mt-2 transition-all duration-500 group-hover:w-16"></div>
            </div>

            <a href="?date={{ \Carbon\Carbon::parse($date)->addDay()->format('Y-m-d') }}" class="w-12 h-12 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-400 hover:border-black hover:text-black transition-all group {{ \Carbon\Carbon::parse($date)->isToday() ? 'opacity-20 pointer-events-none' : '' }}">
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- Minimal Stats -->
        <div class="flex gap-8 border-l border-neutral-100 pl-8">
            <div>
                <span class="block text-2xl font-serif font-bold text-neutral-900">{{ $stats['total'] }}</span>
                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-widest">Dossiers</span>
            </div>
            <div>
                <span class="block text-2xl font-serif font-bold text-[#E6AF5D]">{{ $stats['items'] }}</span>
                <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-widest">Articles</span>
            </div>
        </div>
    </div>

    <!-- 2. THE LEDGER LIST -->
    <div class="bg-white rounded-t-3xl shadow-xl shadow-neutral-100 border border-neutral-100 overflow-hidden min-h-[500px]">
        
        <!-- Header Row -->
        <div class="grid grid-cols-12 gap-4 px-8 py-5 border-b border-neutral-100 bg-neutral-50/50 text-[10px] font-bold text-neutral-400 uppercase tracking-widest sticky top-0 z-20 backdrop-blur-sm">
            <div class="col-span-2 md:col-span-1">Heure</div>
            <div class="col-span-3 md:col-span-2">Réf. BL</div>
            <div class="col-span-4 md:col-span-5">Client & Source</div>
            <div class="col-span-3 md:col-span-2 text-center">Chargement</div>
            <div class="col-span-2 text-right hidden md:block">Statut</div>
        </div>

        <!-- Data Rows -->
        <div class="divide-y divide-neutral-50">
            @forelse($bls as $bl)
            <a href="{{ route('tools.logistics.show', $bl->id) }}" class="ledger-row grid grid-cols-12 gap-4 px-8 py-6 items-center group cursor-pointer block text-decoration-none">
                
                <!-- Time -->
                <div class="col-span-2 md:col-span-1">
                    <span class="font-mono text-xs text-neutral-400 group-hover:text-black transition-colors">{{ $bl->created_at->format('H:i') }}</span>
                </div>

                <!-- Reference -->
                <div class="col-span-3 md:col-span-2">
                    <span class="font-serif font-bold text-lg text-neutral-900 group-hover:text-[#E6AF5D] transition-colors">{{ $bl->bl_number }}</span>
                </div>

                <!-- Client Info -->
                <div class="col-span-4 md:col-span-5">
                    <div class="flex flex-col">
                        <span class="font-medium text-sm text-neutral-800">{{ $bl->client_name }}</span>
                        @if($bl->supplier_name)
                            <span class="text-[10px] text-neutral-400 uppercase tracking-wide mt-0.5">{{ $bl->supplier_name }}</span>
                        @endif
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="col-span-3 md:col-span-2">
                    @php
                        $total = $bl->articles->count();
                        $loaded = $bl->articles->where('status', '!=', 'pending')->count();
                        $percent = $total > 0 ? ($loaded / $total) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-1 bg-neutral-100 rounded-full overflow-hidden">
                            <div class="h-full bg-neutral-900 group-hover:bg-[#E6AF5D] transition-all duration-700" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="text-[9px] font-mono font-bold text-neutral-400 w-8 text-right">{{ $loaded }}/{{ $total }}</span>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="col-span-2 text-right hidden md:block">
                    @php
                        $st = match($bl->status) {
                            'loading' => ['label' => 'En Cours', 'text' => 'text-amber-600'],
                            'loaded' => ['label' => 'Chargé', 'text' => 'text-blue-600'],
                            'delivered' => ['label' => 'Terminé', 'text' => 'text-emerald-600'],
                            'returned' => ['label' => 'Retour', 'text' => 'text-red-600'],
                            default => ['label' => $bl->status, 'text' => 'text-gray-400']
                        };
                    @endphp
                    <span class="text-xs font-bold uppercase tracking-wide {{ $st['text'] }}">{{ $st['label'] }}</span>
                </div>

                <!-- Mobile Chevron (Visible only on small screens) -->
                <div class="col-span-1 md:hidden text-right">
                    <svg class="w-4 h-4 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>

            </a>
            @empty
            <div class="py-24 text-center">
                <div class="w-16 h-16 border-2 border-dashed border-neutral-200 rounded-full flex items-center justify-center mx-auto mb-4 text-neutral-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="text-neutral-900 font-serif text-lg">Registre Vierge</p>
                <p class="text-xs text-neutral-400 mt-1 uppercase tracking-widest">Aucune activité pour cette date</p>
                
                @if(\Carbon\Carbon::parse($date)->isToday())
                <a href="{{ route('tools.logistics.create') }}" class="inline-block mt-6 px-6 py-3 bg-black text-white text-xs font-bold uppercase tracking-widest rounded-lg hover:bg-[#E6AF5D] hover:text-black transition-all">
                    Ouvrir un Dossier
                </a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
    
    <!-- Footer -->
    <div class="text-center mt-12 text-neutral-300 text-xs font-mono uppercase tracking-widest">
        Archive Sécurisée • Diva Ceramica
    </div>

</div>
@endsection