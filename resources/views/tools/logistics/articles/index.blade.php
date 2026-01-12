@extends('layouts.app')

@section('title', 'Registre des Produits')
@section('content')

<div class="max-w-7xl mx-auto pb-20 pt-6">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
        <div>
            <h1 class="text-4xl font-serif text-neutral-900 font-medium tracking-tight">Registre Produits</h1>
            <p class="text-sm font-mono text-neutral-400 mt-2 uppercase tracking-widest">Historique des flux par article</p>
        </div>
        
        <!-- Search -->
        <form action="{{ route('tools.logistics.articles.index') }}" method="GET" class="w-full md:w-96">
            <div class="relative group">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher une référence..." class="w-full bg-transparent border-b border-neutral-300 py-3 pl-2 pr-10 focus:border-black focus:ring-0 transition-colors font-bold text-neutral-900 placeholder-neutral-300">
                <button type="submit" class="absolute right-0 top-3 text-neutral-300 group-hover:text-black transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
            </div>
        </form>
    </div>

    <!-- PRODUCT GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
        <a href="{{ route('tools.logistics.articles.show', ['name' => $product->name]) }}" class="group block bg-white border border-neutral-100 p-8 hover:shadow-xl hover:border-neutral-200 transition-all duration-500 relative overflow-hidden">
            
            <!-- Hover Effect Background -->
            <div class="absolute inset-0 bg-[#F5F5F0] translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-in-out -z-0"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-neutral-400 border border-neutral-100 px-2 py-1 rounded group-hover:bg-white group-hover:text-black transition-colors">
                        {{ $product->reference ?? 'NO-REF' }}
                    </span>
                    <span class="text-[10px] font-bold text-[#E6AF5D] group-hover:text-neutral-900 transition-colors">
                        {{ $product->total_bls }} Dossiers
                    </span>
                </div>

                <h3 class="font-serif text-2xl text-neutral-900 mb-2 leading-tight group-hover:translate-x-1 transition-transform">{{ $product->name }}</h3>
                
                <div class="mt-8 flex items-end justify-between border-t border-neutral-100 pt-4 group-hover:border-neutral-300/50 transition-colors">
                    <div>
                        <span class="block text-[9px] font-bold text-neutral-400 uppercase tracking-widest mb-0.5">Volume Total</span>
                        <span class="font-mono text-lg font-bold text-neutral-900">
                            {{ floatval($product->total_qty) }} <span class="text-xs font-normal text-neutral-500">{{ $product->unit }}</span>
                        </span>
                    </div>
                    <div class="w-8 h-8 rounded-full border border-neutral-200 flex items-center justify-center group-hover:bg-black group-hover:border-black group-hover:text-white transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full py-20 text-center">
            <p class="font-serif text-neutral-400 italic text-lg">Aucun article trouvé dans l'historique.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-12">
        {{ $products->links() }}
    </div>

</div>
@endsection