@extends('layouts.app')

@section('title', 'Nouveautés')
@section('content')

<div class="max-w-7xl mx-auto">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif text-neutral-900 font-bold">Arrivages</h1>
            <p class="text-sm text-neutral-500 mt-1">Dernières références ajoutées au stock.</p>
        </div>
        
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-black text-white rounded-lg text-xs font-bold uppercase tracking-wide hover:bg-[#E6AF5D] hover:text-black transition-all">
                + Ajouter
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($news as $item)
        <div class="group bg-white rounded-2xl border border-neutral-200 overflow-hidden hover:shadow-xl hover:border-[#E6AF5D] transition-all duration-300 flex flex-col h-full">
            <!-- Image Area -->
            <div class="h-48 bg-neutral-100 relative overflow-hidden">
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 flex items-center justify-center text-neutral-300 bg-neutral-50">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                
                @if($item->created_at->diffInDays() < 7)
                    <div class="absolute top-3 left-3 px-2 py-1 bg-black text-white text-[10px] font-bold uppercase tracking-widest rounded-sm shadow-md">New</div>
                @endif
            </div>
            
            <div class="p-5 flex-1 flex flex-col">
                <h3 class="font-bold text-sm text-neutral-900 mb-1 truncate">{{ $item->title }}</h3>
                <p class="text-xs text-neutral-500 line-clamp-2 mb-4 flex-1">{{ $item->description }}</p>
                
                <div class="pt-4 border-t border-neutral-100 flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-xs font-bold text-neutral-900">{{ $item->stock_quantity }} {{ $item->unit }}</span>
                    </div>
                    <span class="text-[10px] text-neutral-400 font-mono">{{ $item->created_at->format('d/m') }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@endsection