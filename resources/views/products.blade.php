@extends('layouts.app')

@section('title', 'Le Catalogue Digital')
@section('page-title', '')

@section('content')

<!-- Typography & Styles -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
    /* --- LAYOUT OVERRIDES --- */
    main {
        padding: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
    }

    /* --- PAGE STYLES --- */
    .full-bleed {
        width: 100%;
        min-height: 100vh;
        background: #050505;
        color: white;
        position: relative;
        overflow-x: hidden;
    }

    /* Custom Cursor */
    #cursor { position: fixed; top: 0; left: 0; width: 20px; height: 20px; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); transition: width 0.3s, height 0.3s, background-color 0.3s; mix-blend-mode: difference; }
    #cursor-dot { position: fixed; top: 0; left: 0; width: 4px; height: 4px; background-color: #E6AF5D; border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); }
    
    /* Elements */
    .glass-panel { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); }
    .text-gold { color: #E6AF5D; }
    .font-serif { font-family: 'Playfair Display', serif; }
    .noise { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 0; opacity: 0.05; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
</style>

<div class="full-bleed">
    <div id="cursor" class="hidden md:block"></div><div id="cursor-dot" class="hidden md:block"></div>
    <div class="noise"></div>

    <!-- 1. HERO SECTION -->
    <section class="relative h-screen flex flex-col justify-center px-8 md:px-24 z-10 border-b border-white/5">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#E6AF5D]/10 rounded-full blur-[100px] opacity-30 pointer-events-none animate-pulse"></div>
        
        <span class="text-xs font-mono text-[#E6AF5D] tracking-[0.3em] uppercase mb-6 opacity-0 fade-in">CATALOGUE LIVE</span>
        
        <h1 class="text-6xl md:text-8xl font-serif leading-[0.9] mb-8">
            <div class="overflow-hidden"><span class="block reveal-text">Produits</span></div>
            <div class="overflow-hidden"><span class="block reveal-text italic text-neutral-500">Disponibles.</span></div>
        </h1>

        <p class="max-w-xl text-neutral-400 text-sm leading-loose opacity-0 fade-in pl-4 border-l border-[#E6AF5D]/30">
            Découvrez nos arrivages et nouveautés en temps réel. Stock et localisation affichés pour chaque référence.
        </p>

        <!-- SEARCH BAR -->
        <form method="GET" action="{{ route('products.catalog') }}" class="mt-12 opacity-0 fade-in max-w-2xl">
            <div class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Rechercher un produit..." 
                    class="w-full bg-white/5 border border-white/10 rounded-full px-8 py-4 pl-14 text-white placeholder-neutral-500 focus:ring-2 focus:ring-[#E6AF5D] focus:border-transparent backdrop-blur-xl">
                <svg class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </form>
    </section>

    <!-- 2. PRODUCTS GRID -->
    <section class="py-32 px-8 md:px-24 relative z-10">
        @if($products->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($products as $item)
            <div class="glass-panel rounded-xl overflow-hidden group hover:border-[#E6AF5D]/30 transition-all duration-300 hover:scale-[1.02]">
                <!-- Image -->
                <div class="h-64 bg-neutral-900 relative overflow-hidden">
                    @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    
                    <!-- Stock Badge -->
                    @if($item->stock_quantity)
                        @php
                            $stock = $item->stock_quantity;
                            $isLow = $stock > 0 && $stock < 100;
                            $isOut = $stock == 0;
                        @endphp
                        <div class="absolute top-4 left-4 px-3 py-2 {{ $isOut ? 'bg-red-500' : ($isLow ? 'bg-amber-500' : 'bg-emerald-500') }} text-white text-xs font-bold rounded-lg shadow-lg backdrop-blur-sm flex items-center gap-2">
                            <span class="relative flex h-2 w-2">
                                @if(!$isOut)
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                @endif
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                            </span>
                            {{ number_format($stock, 0, ',', ' ') }} {{ $item->unit }}
                        </div>
                    @endif

                    <!-- Warehouse Badge -->
                    @if($item->warehouse)
                    <div class="absolute bottom-4 right-4 bg-black/60 backdrop-blur-md border border-white/20 px-3 py-2 rounded-lg flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-xs font-bold text-white">{{ $item->warehouse }}</span>
                    </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-6">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-[#E6AF5D] mb-2">{{ $item->created_at->format('d M Y') }}</p>
                    <h3 class="font-serif font-bold text-xl text-white mb-2 group-hover:text-[#E6AF5D] transition-colors">{{ $item->title }}</h3>
                    @if($item->description)
                        <p class="text-sm text-neutral-400 leading-relaxed line-clamp-2">{{ $item->description }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
            <div class="glass-panel rounded-2xl p-20 text-center max-w-2xl mx-auto">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="font-serif font-bold text-2xl text-white mb-2">Catalogue Vide</h3>
                <p class="text-neutral-500">Aucun produit disponible pour le moment.</p>
            </div>
        @endif
    </section>

</div>

<!-- GSAP Animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // Cursor Logic (Desktop Only)
        if (window.innerWidth > 768) {
            const cursor = document.getElementById('cursor'), cursorDot = document.getElementById('cursor-dot');
            document.addEventListener('mousemove', e => { 
                gsap.to(cursor, {x: e.clientX, y: e.clientY, duration: 0.2}); 
                gsap.to(cursorDot, {x: e.clientX, y: e.clientY, duration: 0.1}); 
            });
        }

        // Hero Animations
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.reveal-text', { y: 150, duration: 1.5, stagger: 0.1, rotateX: 10 })
          .to('.fade-in', { opacity: 1, y: 0, duration: 1, stagger: 0.2 }, "-=1");

        // Scroll Triggers
        gsap.utils.toArray('section').forEach(section => {
            gsap.from(section.querySelectorAll('.glass-panel'), {
                scrollTrigger: { trigger: section, start: 'top 80%' },
                y: 50,
                opacity: 0,
                duration: 1,
                stagger: 0.1
            });
        });
    });
</script>

@endsection