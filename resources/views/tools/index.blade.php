@extends('layouts.app')

@section('title', 'Hub Outils')
@section('page-title', 'Ecosystème Digital')

@section('content')

<!-- Dependencies -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    .font-serif { font-family: 'Playfair Display', serif; }
    
    /* Card Styles */
    .tool-card {
        border: 1px solid #E5E5E5;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Bouncy spring feel */
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        opacity: 0; /* Hidden initially for GSAP */
        transform: translateY(20px);
    }
    
    .tool-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px -5px rgba(230, 175, 93, 0.15); /* Golden shadow on hover */
        border-color: #E6AF5D;
    }

    .icon-box {
        transition: all 0.3s ease;
    }
    
    .tool-card:hover .icon-box {
        background-color: #000;
        color: #fff;
        transform: scale(1.1) rotate(-3deg);
    }

    /* Roadmap Timeline */
    .roadmap-line {
        width: 1px;
        background: linear-gradient(to bottom, #E6AF5D 0%, rgba(230, 175, 93, 0) 100%);
        position: absolute;
        left: 50%;
        top: 0;
        height: 80px;
    }
</style>

<div class="max-w-7xl mx-auto pb-20 px-2">
    
    <!-- 1. HEADER -->
    <div class="mb-12 border-b border-neutral-100 pb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-serif text-neutral-900">Workbench</h2>
            <p class="text-sm text-neutral-500 mt-2 max-w-md">Suite d'outils opérationnels pour la gestion commerciale et logistique.</p>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-neutral-900 text-[#E6AF5D] text-[10px] font-bold uppercase tracking-widest rounded-full border border-neutral-800">
                Live v2.4
            </span>
        </div>
    </div>

    <!-- 2. ACTIVE TOOLS GRID -->
    <!-- Added p-2 to container to prevent hover shadow clipping -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-24 p-2">
        
        <!-- CARD 1: LABEL GENERATOR (Sales) -->
        <a href="{{ route('tools.label') }}" class="block">
            <article class="tool-card rounded-3xl p-8 relative overflow-hidden group">
                <!-- Hover Glow Effect -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#E6AF5D]/10 rounded-full blur-3xl transition-opacity opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                
                <div class="flex justify-between items-start mb-8">
                    <div class="icon-box w-14 h-14 rounded-2xl bg-neutral-50 text-neutral-900 flex items-center justify-center shadow-sm border border-neutral-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-[#E6AF5D] bg-[#E6AF5D]/10 px-2 py-1 rounded">Showroom</span>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-neutral-900 mb-2">Générateur Étiquettes</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Création d'étiquettes QR. Redirection des clients showroom vers le dispatching WhatsApp.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-neutral-50 flex items-center text-xs font-bold text-neutral-900 uppercase tracking-widest group-hover:text-[#E6AF5D] transition-colors">
                    Ouvrir <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </article>
        </a>

        <!-- CARD 2: LOGISTICS / BL (Operations) -->
        <a href="{{ route('tools.logistics.index') }}" class="block">
            <article class="tool-card rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl transition-opacity opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                
                <div class="flex justify-between items-start mb-8">
                    <div class="icon-box w-14 h-14 rounded-2xl bg-neutral-50 text-neutral-900 flex items-center justify-center shadow-sm border border-neutral-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-blue-600 bg-blue-50 px-2 py-1 rounded">Nouveau</span>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-neutral-900 mb-2">Suivi Logistique</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Tracking des Bons de Livraison (BL) en temps réel. Statuts de chargement et gestion des départs.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-neutral-50 flex items-center text-xs font-bold text-neutral-900 uppercase tracking-widest group-hover:text-blue-600 transition-colors">
                    Accéder <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </article>
        </a>

        <!-- CARD 3: SAV / INCIDENTS (Quality) -->
        <a href="{{ route('tools.sav.index') }}" class="block">
            <article class="tool-card rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-500/10 rounded-full blur-3xl transition-opacity opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                
                <div class="flex justify-between items-start mb-8">
                    <div class="icon-box w-14 h-14 rounded-2xl bg-neutral-50 text-neutral-900 flex items-center justify-center shadow-sm border border-neutral-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-red-600 bg-red-50 px-2 py-1 rounded">Priorité</span>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-neutral-900 mb-2">Gestion SAV & Casse</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Déclaration d'incidents et retours marchandises. Suivi des remplacements et avoirs.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-neutral-50 flex items-center text-xs font-bold text-neutral-900 uppercase tracking-widest group-hover:text-red-600 transition-colors">
                    Signaler <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </article>
        </a>

        <!-- CARD 4: NEWS HUB (Communication) -->
        <a href="{{ route('tools.news.index') }}" class="block">
            <article class="tool-card rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl transition-opacity opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                
                <div class="flex justify-between items-start mb-8">
                    <div class="icon-box w-14 h-14 rounded-2xl bg-neutral-50 text-neutral-900 flex items-center justify-center shadow-sm border border-neutral-100">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-neutral-400 bg-neutral-100 px-2 py-1 rounded">Info</span>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-neutral-900 mb-2">Hub Nouveautés</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Fil d'actualité des arrivages. Soyez notifié dès la réception d'une nouvelle collection.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-neutral-50 flex items-center text-xs font-bold text-neutral-900 uppercase tracking-widest group-hover:text-emerald-600 transition-colors">
                    Consulter <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </article>
        </a>

    </div>

        <!-- CARD 5: AI VISUALIZER (Innovation) -->
        <a href="{{ route('tools.visualizer.index') }}" class="block">
            <article class="tool-card rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-[#E6AF5D]/20 rounded-full blur-3xl transition-opacity opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                
                <div class="flex justify-between items-start mb-8">
                    <div class="icon-box w-14 h-14 rounded-2xl bg-neutral-900 text-[#E6AF5D] flex items-center justify-center shadow-sm border border-neutral-800">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide text-white bg-black px-2 py-1 rounded">Beta</span>
                </div>
                
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-neutral-900 mb-2">Visualisateur IA</h3>
                    <p class="text-sm text-neutral-500 leading-relaxed">
                        Projection instantanée de carrelage sur photo client. Outil d'aide à la vente par Intelligence Artificielle.
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-neutral-50 flex items-center text-xs font-bold text-neutral-900 uppercase tracking-widest group-hover:text-[#E6AF5D] transition-colors">
                    Lancer <svg class="w-3 h-3 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </article>
        </a>

    </div>

</div>

<style>
    @keyframes scan {
        0%, 100% { top: 0%; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        50% { top: 100%; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        gsap.registerPlugin(ScrollTrigger);

        // 1. Grid Animation (Simple & Reliable)
        gsap.to('.tool-card', {
            y: 0,
            opacity: 1,
            duration: 0.8,
            stagger: 0.1,
            ease: 'power3.out',
            delay: 0.1
        });

        // 2. Roadmap Reveal (On Scroll)
        gsap.to('#innovation-card', {
            y: 0,
            opacity: 1,
            duration: 1,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: '#innovation-card',
                start: 'top 85%'
            }
        });
        
        gsap.from('.roadmap-line', {
            height: 0,
            duration: 1.5,
            ease: 'power2.inOut',
            scrollTrigger: { trigger: '.roadmap-line', start: 'top 80%' }
        });
    });
</script>

@endsection