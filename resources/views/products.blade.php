@extends('layouts.app')

@section('title', 'Le Catalogue Digital')
@section('page-title', '')

@section('content')

<!-- Typography & Styles -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=Inter:wght@300;400;500&display=swap" rel="stylesheet">

<style>
    /* --- LAYOUT OVERRIDES --- */
    /* This forces the main container to be full width and removes padding just for this page */
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
        
        <span class="text-xs font-mono text-[#E6AF5D] tracking-[0.3em] uppercase mb-6 opacity-0 fade-in">SYS 3.0</span>
        
        <h1 class="text-6xl md:text-8xl font-serif leading-[0.9] mb-8">
            <div class="overflow-hidden"><span class="block reveal-text">L'Architecture</span></div>
            <div class="overflow-hidden"><span class="block reveal-text italic text-neutral-500">Invisible.</span></div>
        </h1>

        <p class="max-w-xl text-neutral-400 text-sm leading-loose opacity-0 fade-in pl-4 border-l border-[#E6AF5D]/30">
            Une plateforme unifiée pour visualiser, présenter et vérifier la disponibilité de nos collections exclusives. Le catalogue papier appartient au passé.
        </p>

        <div class="mt-12 opacity-0 fade-in">
            <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full border border-white/10 bg-white/5 backdrop-blur-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-mono tracking-widest text-neutral-300">DEVELOPMENT STATUS: <span class="text-white font-bold">BRAINSTORMING</span></span>
            </div>
        </div>
    </section>

    <!-- 2. FEATURE: AR VISUALIZATION -->
    <section class="py-32 px-8 md:px-24 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="order-2 lg:order-1">
                <div class="glass-panel w-full aspect-[4/5] rounded-lg overflow-hidden relative group">
                    <!-- Abstract Representation of a Tile -->
                    <div class="absolute inset-0 bg-neutral-900 flex items-center justify-center">
                        <div class="w-3/4 h-3/4 border border-white/5 relative overflow-hidden">
                            <!-- Scanning Line -->
                            <div class="absolute top-0 left-0 w-full h-1 bg-[#E6AF5D] shadow-[0_0_20px_#E6AF5D] z-20 scan-anim"></div>
                            
                            <!-- Texture Image (Abstract) -->
                            <div class="absolute inset-0 bg-neutral-800 opacity-40 group-hover:opacity-80 transition-opacity duration-700 bg-[url('https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=1000&auto=format&fit=crop')] bg-cover bg-center"></div>
                            
                            <!-- AR HUD -->
                            <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
                                <div class="text-[10px] font-mono text-[#E6AF5D]">
                                    DIM: 120x240<br>REF: MRB-09X
                                </div>
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="order-1 lg:order-2 space-y-8">
                <span class="text-7xl font-serif text-white/5 font-bold">01</span>
                <h2 class="text-4xl font-light text-white">Visualisation <span class="text-[#E6AF5D] italic font-serif">Immersive</span></h2>
                <p class="text-neutral-400 text-sm leading-loose">
                    Ne dites plus "imaginez". Montrez.
                    <br><br>
                    Le nouveau module catalogue intègre une technologie de rendu en temps réel. Sélectionnez une référence et visualisez instantanément son rendu : textures 4K, reflets lumineux et raccords dynamiques.
                </p>
            </div>
        </div>
    </section>

    <!-- 3. FEATURE: REAL TIME STOCK -->
    <section class="py-32 px-8 md:px-24 bg-neutral-900/30 border-y border-white/5 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            
            <div class="space-y-8">
                <span class="text-7xl font-serif text-white/5 font-bold">02</span>
                <h2 class="text-4xl font-light text-white">Disponibilité <span class="text-[#E6AF5D] italic font-serif">Absolue</span></h2>
                <p class="text-neutral-400 text-sm leading-loose">
                    Fini les appels à l'entrepôt. Le catalogue est connecté en direct à l'API de gestion des stocks.
                    <br><br>
                    Vous savez instantanément ce qui est disponible et ce qui est en rupture de stock. Sécurisez vos ventes grâce à des données fiables, mises à jour en temps réel.
                </p>
            </div>

            <!-- Data Visual -->
            <div class="glass-panel p-8 rounded-xl relative overflow-hidden">
                <div class="flex justify-between items-center mb-8 pb-4 border-b border-white/5">
                    <span class="text-xs font-mono text-neutral-500">LIVE FEED</span>
                    <div class="flex gap-1">
                        <div class="w-1 h-1 bg-emerald-500 rounded-full animate-ping"></div>
                        <div class="w-1 h-1 bg-emerald-500 rounded-full"></div>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <!-- Row 1 -->
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded border border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-neutral-800 rounded flex items-center justify-center text-[8px] text-neutral-500">IMG</div>
                            <div>
                                <div class="text-xs font-bold text-white">REF MONOVAR CREMA BR 120x60</div>
                                <div class="text-[10px] text-neutral-500">REF: 120MIMOCRBR</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-emerald-400">1,240 m²</div>
                            <div class="text-[8px] text-neutral-500">MEDIOUNA</div>
                        </div>
                    </div>
                    
                    <!-- Row 2 -->
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded border border-white/5 opacity-60">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-neutral-800 rounded flex items-center justify-center text-[8px] text-neutral-500">IMG</div>
                            <div>
                                <div class="text-xs font-bold text-white">REF MILA GREY SATIN 120x60</div>
                                <div class="text-[10px] text-neutral-500">Ref: 120MIMIGRST</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-emerald-400">520 m²</div>
                            <div class="text-[8px] text-neutral-500">MEDIOUNA</div>
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded border border-white/5 opacity-40">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-neutral-800 rounded flex items-center justify-center text-[8px] text-neutral-500">IMG</div>
                            <div>
                                <div class="text-xs font-bold text-white">MEUBLE YB-2024-008-2D 160x52</div>
                                <div class="text-[10px] text-neutral-500">Ref: MEYA0082D</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-red-500">0 m²</div>
                            <div class="text-[8px] text-neutral-500">RUPTURE</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 4. FOOTER / TIMELINE -->
    <footer class="py-24 px-8 text-center relative z-10">
        <div class="inline-block p-1 rounded-full border border-white/10 mb-8">
            <span class="block px-6 py-2 bg-neutral-900 rounded-full text-xs font-bold tracking-widest text-[#E6AF5D] uppercase">
                Lancement APPX Q1 2026
            </span>
        </div>
        <h3 class="text-2xl font-serif italic text-neutral-500">"DIVA CERAMICA 2026"</h3>
    </footer>

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

        // Scan Animation
        gsap.to('.scan-anim', {
            top: '100%',
            duration: 3,
            repeat: -1,
            ease: 'linear',
            yoyo: true
        });

        // Scroll Triggers
        gsap.utils.toArray('section').forEach(section => {
            gsap.from(section.querySelectorAll('h2, p, .glass-panel'), {
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