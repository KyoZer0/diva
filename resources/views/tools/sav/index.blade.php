@extends('layouts.app')

@section('title', 'Service Qualité')
@section('page-title', '')

@section('content')

<div class="max-w-7xl mx-auto h-[calc(100vh-100px)] flex flex-col">
    
    <!-- 1. HEADER & KPI -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 shrink-0">
        <div>
            <h1 class="text-3xl font-serif text-neutral-900 font-bold">Service Qualité</h1>
            <p class="text-sm text-neutral-500 mt-1">Gestion des retours, casses et anomalies.</p>
        </div>
        
        <div class="flex gap-4">
            <div class="bg-white px-5 py-3 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">En Cours</span>
                <span class="text-xl font-serif font-bold text-[#E6AF5D]">{{ $incidents->where('status', 'reported')->count() }}</span>
            </div>
            <div class="bg-white px-5 py-3 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-3">
                <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-widest">Résolus</span>
                <span class="text-xl font-serif font-bold text-neutral-900">{{ $incidents->where('status', 'resolved')->count() }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 flex-1 overflow-hidden">
        
        <!-- 2. DECLARATION FORM (Left Panel - 4 Cols) -->
        <div class="lg:col-span-4 bg-white p-8 rounded-3xl border border-neutral-200 shadow-lg flex flex-col overflow-y-auto">
            
            <div class="mb-8 border-b border-neutral-100 pb-4">
                <h2 class="font-bold text-lg text-neutral-900">Nouveau Rapport</h2>
                <p class="text-xs text-neutral-400 mt-1">Déclarez une anomalie de stock ou de livraison.</p>
            </div>

            <form action="{{ route('tools.sav.store') }}" method="POST" class="space-y-6 flex-1">
                @csrf
                
                <!-- Article Input -->
                <div class="group">
                    <label class="block text-[10px] font-bold text-neutral-400 uppercase mb-2 group-focus-within:text-[#E6AF5D] transition-colors">Article Concerné</label>
                    <div class="relative">
                        <input type="text" name="article_name" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3.5 text-sm font-medium focus:outline-none focus:border-[#E6AF5D] focus:ring-1 focus:ring-[#E6AF5D] transition-all" placeholder="Rechercher une référence..." required>
                        <svg class="w-4 h-4 text-neutral-400 absolute right-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <!-- Qty & Date Row -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="group">
                        <label class="block text-[10px] font-bold text-neutral-400 uppercase mb-2 group-focus-within:text-[#E6AF5D] transition-colors">Quantité</label>
                        <input type="number" name="quantity" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3.5 text-sm font-medium focus:outline-none focus:border-[#E6AF5D] transition-all" placeholder="0.00" step="0.01" required>
                    </div>
                    <div class="group">
                        <label class="block text-[10px] font-bold text-neutral-400 uppercase mb-2 group-focus-within:text-[#E6AF5D] transition-colors">Date Constat</label>
                        <input type="date" name="date" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3.5 text-sm font-medium focus:outline-none focus:border-[#E6AF5D] transition-all text-neutral-600" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Description -->
                <div class="group">
                    <label class="block text-[10px] font-bold text-neutral-400 uppercase mb-2 group-focus-within:text-[#E6AF5D] transition-colors">Observations</label>
                    <textarea name="notes" rows="4" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3.5 text-sm font-medium focus:outline-none focus:border-[#E6AF5D] transition-all resize-none" placeholder="Détails du dommage (Casse, Teinte, Dimension...)"></textarea>
                </div>

                <!-- File Upload (Visual Only for now) -->
                <div class="border-2 border-dashed border-neutral-200 rounded-xl p-6 text-center hover:bg-neutral-50 transition-colors cursor-pointer group">
                    <svg class="w-6 h-6 mx-auto text-neutral-300 group-hover:text-[#E6AF5D] transition-colors mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs text-neutral-400">Ajouter des photos (Optionnel)</p>
                </div>

                <div class="mt-auto pt-4">
                    <button type="submit" class="w-full py-4 bg-neutral-900 hover:bg-black text-white font-bold rounded-xl text-xs uppercase tracking-widest transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 text-[#E6AF5D] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Enregistrer Incident
                    </button>
                </div>
            </form>
        </div>

        <!-- 3. HISTORY LIST (Right Panel - 8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl border border-neutral-200 shadow-sm flex flex-col overflow-hidden">
            
            <!-- List Header -->
            <div class="p-6 border-b border-neutral-100 bg-neutral-50/30 flex justify-between items-center">
                <div class="flex gap-4">
                    <button class="text-sm font-bold text-neutral-900 border-b-2 border-black pb-1">Tous</button>
                    <button class="text-sm font-medium text-neutral-400 hover:text-neutral-600 transition-colors">En Attente</button>
                    <button class="text-sm font-medium text-neutral-400 hover:text-neutral-600 transition-colors">Archives</button>
                </div>
                <!-- Search -->
                <div class="relative w-64">
                    <input type="text" placeholder="Filtrer..." class="w-full bg-white border border-neutral-200 rounded-lg py-1.5 pl-8 pr-4 text-xs focus:outline-none focus:border-[#E6AF5D]">
                    <svg class="w-3 h-3 text-neutral-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- List Content -->
            <div class="flex-1 overflow-auto custom-scrollbar p-2">
                <div class="space-y-2">
                    @forelse($incidents as $incident)
                        <div class="group p-4 rounded-xl border border-neutral-100 hover:border-[#E6AF5D] hover:shadow-md transition-all bg-white flex items-center justify-between">
                            
                            <div class="flex items-center gap-4">
                                <!-- Status Icon -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $incident->status == 'resolved' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    @if($incident->status == 'resolved')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-bold text-sm text-neutral-900 group-hover:text-[#E6AF5D] transition-colors">{{ $incident->article_name }}</h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100">-{{ $incident->quantity }}</span>
                                        <span class="text-[10px] text-neutral-400">{{ \Carbon\Carbon::parse($incident->date)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                <!-- Notes Preview -->
                                @if($incident->notes)
                                    <p class="text-xs text-neutral-500 italic max-w-xs truncate hidden md:block">
                                        "{{ $incident->notes }}"
                                    </p>
                                @endif

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $incident->status == 'resolved' ? 'bg-neutral-100 text-neutral-500' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $incident->status == 'resolved' ? 'Clôturé' : 'En Traitement' }}
                                    </span>
                                    <button class="p-2 text-neutral-300 hover:text-black transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </div>
                            </div>

                        </div>
                    @empty
                        <div class="h-64 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 rounded-full bg-neutral-50 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-neutral-900 font-medium">Tout est conforme</h3>
                            <p class="text-xs text-neutral-400 mt-1">Aucune anomalie signalée récemment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar for the table container */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E5E5; border-radius: 2px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #E6AF5D; }
</style>

@endsection