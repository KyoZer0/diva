@extends('layouts.app')

@section('title', 'Nouveautés - Cockpit Commercial')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-20">

    <!-- HEADER & NAV -->
    <div class="flex justify-between items-center">
        <a href="{{ route('tools.sales.index') }}" class="inline-flex items-center text-sm font-bold text-neutral-400 hover:text-neutral-900 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour au Tableau de Bord
        </a>
        <h1 class="text-2xl font-serif font-bold text-neutral-900">Nouveautés & Arrivages</h1>
    </div>

    <!-- NEWS GRID -->
    @if($news->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($news as $item)
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-neutral-100 group hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <!-- Image -->
            <div class="h-64 bg-neutral-100 relative overflow-hidden">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-neutral-900 pattern-grid-lg">
                        <svg class="w-16 h-16 text-[#E6AF5D] opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>

                @if($item->created_at->diffInDays() < 7)
                    <div class="absolute top-4 left-4 px-3 py-1 bg-[#E6AF5D] text-black text-[10px] font-bold uppercase tracking-widest rounded-full shadow-lg">Nouveau</div>
                @endif

                @if($item->stock_quantity)
                <div class="absolute bottom-4 right-4 bg-white/10 backdrop-blur-md border border-white/20 px-4 py-2 rounded-xl text-white text-xs font-bold shadow-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    {{ $item->stock_quantity }} {{ $item->unit }}
                </div>
                @endif
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="flex items-center gap-2 mb-3">
                    <span class="block w-6 h-[1px] bg-[#E6AF5D]"></span>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-neutral-400">{{ $item->created_at->format('d M Y') }}</p>
                </div>
                <h3 class="font-serif font-bold text-2xl text-neutral-900 mb-3 leading-tight group-hover:text-[#E6AF5D] transition-colors">{{ $item->title }}</h3>
                <p class="text-sm text-neutral-500 leading-relaxed line-clamp-3 mb-4">
                    {{ $item->description }}
                </p>
                
                @if($item->warehouse)
                <div class="flex items-center gap-2 text-xs text-neutral-600 bg-neutral-50 rounded-lg px-3 py-2 w-fit">
                    <svg class="w-4 h-4 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="font-bold">{{ $item->warehouse }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @else
         <div class="bg-neutral-50 rounded-[2rem] p-16 text-center border-2 border-dashed border-neutral-200">
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 text-neutral-300 shadow-sm">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
            </div>
            <h3 class="font-serif font-bold text-xl text-neutral-900">Tout est calme</h3>
            <p class="text-neutral-500 mt-2 max-w-sm mx-auto">Aucun nouvel arrivage ou annonce pour le moment. Revenez un peu plus tard.</p>
        </div>
    @endif

</div>
@endsection
