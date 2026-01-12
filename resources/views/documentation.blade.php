<!DOCTYPE html>
<html lang="fr" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Brand Book & Protocole | Diva Ceramica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- GSAP + ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- SplitType for Text Animations -->
    <script src="https://unpkg.com/split-type"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: { 
                    colors: { gold: '#E6AF5D', dark: '#050505', glass: 'rgba(255,255,255,0.03)' }, 
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] } 
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap');
        
        body { background-color: #050505; color: white; cursor: none; overflow-x: hidden; }
        
        /* Cursor */
        #cursor { position: fixed; top: 0; left: 0; width: 20px; height: 20px; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); transition: width 0.3s, height 0.3s, background-color 0.3s; mix-blend-mode: difference; }
        #cursor-dot { position: fixed; top: 0; left: 0; width: 4px; height: 4px; background-color: #E6AF5D; border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); }
        
        /* Texture */
        .noise { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; opacity: 0.04; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
        
        /* Utilities */
        .glass-panel { background: linear-gradient(180deg, rgba(255,255,255,0.03) 0%, rgba(255,255,255,0.01) 100%); border: 1px solid rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); }
        .text-gradient { background: linear-gradient(to right, #fff, #999); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .line-mask { clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%); }
    </style>
</head>
<body class="selection:bg-gold selection:text-black">
    <div id="cursor"></div><div id="cursor-dot"></div>
    <div class="noise"></div>
    
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 px-8 py-6 flex justify-between items-center mix-blend-difference">
        <a href="{{ route('home') }}" class="text-xs font-bold tracking-[0.2em] uppercase hover:text-gold transition-colors">Diva Ceramica</a>
        <a href="{{ route('login') }}" class="text-[10px] font-mono border border-white/20 px-4 py-2 rounded-full hover:bg-white hover:text-black transition-all">SYSTEM ACCESS</a>
    </nav>

    <main class="relative z-10 w-full">
        
        <!-- 1. HERO: The Philosophy -->
        <section class="min-h-screen flex flex-col justify-center px-6 md:px-20 pt-20">
            <div class="max-w-4xl">
                <span class="inline-block text-gold text-xs font-mono tracking-widest mb-6 opacity-0 fade-in">EST. MAROC</span>
                <h1 class="text-5xl md:text-8xl font-serif font-medium leading-[1.1] mb-8 split-text">
                    L'Élégance n'est pas <br> un détail. <span class="text-neutral-600 italic">C'est une fondation.</span>
                </h1>
                <p class="max-w-xl text-neutral-400 text-sm md:text-base leading-relaxed opacity-0 fade-in pl-1 border-l border-gold/50">
                    Bienvenue dans l'espace de formation Diva Ceramica. Ici, nous ne vendons pas simplement du carrelage ou du sanitaire. Nous façonnons des espaces de vie d'exception pour une clientèle exigeante.
                </p>
            </div>
        </section>

        <!-- 2. BRAND PILLARS (Horizontal Scroll) -->
        <section class="py-32 border-t border-white/5 bg-neutral-900/30 overflow-hidden">
            <div class="px-6 md:px-20 mb-16">
                <h2 class="text-xs font-mono text-neutral-500 uppercase tracking-widest mb-2">01 — L'ADN DIVA</h2>
                <h3 class="text-4xl md:text-5xl font-light">Nos Piliers d'Excellence</h3>
            </div>
            
            <div class="flex flex-col md:flex-row gap-6 px-6 md:px-20">
                <!-- Card 1 -->
                <div class="glass-panel p-10 rounded-xl flex-1 group hover:border-gold/30 transition-colors duration-500 pillar-card">
                    <div class="w-12 h-12 mb-8 bg-gradient-to-br from-neutral-800 to-black rounded-lg flex items-center justify-center border border-white/5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h4 class="text-xl font-serif italic text-white mb-4">Curating Rare</h4>
                    <p class="text-sm text-neutral-400 leading-loose">
                        Notre catalogue est une sélection rigoureuse. Nous proposons des textures et des formats que l'on ne trouve pas ailleurs. Votre rôle est d'éduquer le client sur cette unicité.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="glass-panel p-10 rounded-xl flex-1 group hover:border-gold/30 transition-colors duration-500 pillar-card">
                    <div class="w-12 h-12 mb-8 bg-gradient-to-br from-neutral-800 to-black rounded-lg flex items-center justify-center border border-white/5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="text-xl font-serif italic text-white mb-4">Conseil Expert</h4>
                    <p class="text-sm text-neutral-400 leading-loose">
                        Nous ne sommes pas des vendeurs, mais des consultants. L'outil CRM doit refléter cette approche : notez les détails techniques, les inspirations et les contraintes du client.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="glass-panel p-10 rounded-xl flex-1 group hover:border-gold/30 transition-colors duration-500 pillar-card">
                    <div class="w-12 h-12 mb-8 bg-gradient-to-br from-neutral-800 to-black rounded-lg flex items-center justify-center border border-white/5 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h4 class="text-xl font-serif italic text-white mb-4">Réactivité</h4>
                    <p class="text-sm text-neutral-400 leading-loose">
                        La différence entre un devis et une vente est souvent la vitesse. Utilisez la fonction <strong>"Action Rapide"</strong> pour clore vos dossiers dès la validation.
                    </p>
                </div>
            </div>
        </section>

        <!-- 3. THE PROTOCOL (Visual Guide) -->
        <section class="py-32 px-6 md:px-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                
                <!-- Text Content -->
                <div class="space-y-12">
                    <div>
                        <h2 class="text-xs font-mono text-neutral-500 uppercase tracking-widest mb-2">02 — PROTOCOLE CRM</h2>
                        <h3 class="text-4xl md:text-5xl font-light mb-6">Maîtriser la Donnée</h3>
                        <p class="text-neutral-400 leading-relaxed">
                            Le système interne V.2.0 a été conçu pour simplifier votre quotidien tout en maximisant la connaissance client. Voici les règles d'or de la saisie.
                        </p>
                    </div>

                    <div class="space-y-8">
                        <div class="flex gap-6 group protocol-item">
                            <span class="text-2xl font-serif text-neutral-700 group-hover:text-gold transition-colors">01</span>
                            <div>
                                <h4 class="text-white font-medium mb-1">Qualification Complète</h4>
                                <p class="text-sm text-neutral-500">Un client sans "Source" ou sans "Ville" est une donnée perdue. Remplissez tous les champs pour permettre un ciblage précis.</p>
                            </div>
                        </div>
                        <div class="flex gap-6 group protocol-item">
                            <span class="text-2xl font-serif text-neutral-700 group-hover:text-gold transition-colors">02</span>
                            <div>
                                <h4 class="text-white font-medium mb-1">Le Cycle de Vie</h4>
                                <p class="text-sm text-neutral-500">
                                    <span class="text-white bg-neutral-800 px-1.5 py-0.5 text-[10px] rounded">VISITÉ</span> Premier contact.<br>
                                    <span class="text-gold bg-gold/10 px-1.5 py-0.5 text-[10px] rounded">DEVIS</span> Intérêt confirmé.<br>
                                    <span class="text-emerald-400 bg-emerald-900/30 px-1.5 py-0.5 text-[10px] rounded">VENDU</span> Finalisation.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-6 group protocol-item">
                            <span class="text-2xl font-serif text-neutral-700 group-hover:text-gold transition-colors">03</span>
                            <div>
                                <h4 class="text-white font-medium mb-1">Notes Qualitatives</h4>
                                <p class="text-sm text-neutral-500">Utilisez le champ "Remarques" pour noter le style recherché (ex: "Marbre noir", "Minimaliste"). C'est la clé du conseil personnalisé.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abstract Visual -->
                <div class="relative h-[600px] w-full glass-panel rounded-2xl overflow-hidden flex items-center justify-center" id="visual-container">
                    <!-- Dynamic Grid representing tiles -->
                    <div class="absolute inset-0 grid grid-cols-3 grid-rows-3 gap-1 p-4 opacity-50">
                        <div class="bg-white/5 tile-anim"></div>
                        <div class="bg-white/5 tile-anim"></div>
                        <div class="bg-gold/20 tile-anim"></div>
                        <div class="bg-white/5 tile-anim"></div>
                        <div class="bg-white/10 tile-anim flex items-center justify-center">
                            <span class="text-xs font-mono text-gold">DATA</span>
                        </div>
                        <div class="bg-white/5 tile-anim"></div>
                        <div class="bg-white/5 tile-anim"></div>
                        <div class="bg-neutral-800 tile-anim"></div>
                        <div class="bg-white/5 tile-anim"></div>
                    </div>
                    
                    <!-- Floating Data Card -->
                    <div class="absolute w-64 bg-[#0A0A0A] border border-neutral-800 p-6 rounded-xl shadow-2xl transform rotate-3 hover:rotate-0 transition-transform duration-500 z-20">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-full bg-gold text-black flex items-center justify-center font-bold text-xs">AB</div>
                            <div>
                                <div class="h-2 w-20 bg-neutral-800 rounded mb-1"></div>
                                <div class="h-1.5 w-12 bg-neutral-800 rounded"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="h-8 w-full bg-neutral-900 rounded border border-neutral-800 flex items-center px-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            </div>
                            <div class="flex gap-2">
                                <div class="h-6 w-1/2 bg-neutral-900 rounded"></div>
                                <div class="h-6 w-1/2 bg-neutral-900 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- 4. QUOTE -->
        <section class="py-20 text-center border-y border-white/5 bg-neutral-900/20">
            <p class="text-2xl md:text-4xl font-serif italic text-neutral-300 mb-6 max-w-3xl mx-auto leading-normal quote-anim">
                "Le carrelage habille l'espace, mais c'est le service qui habille l'expérience."
            </p>
            <span class="text-xs font-mono text-gold tracking-widest uppercase">Direction Diva Ceramica</span>
        </section>

        <!-- Footer -->
        <footer class="py-20 text-center">
            <p class="text-neutral-500 text-sm mb-8">Système réservé au personnel autorisé.</p>
            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-black font-bold rounded-full overflow-hidden transition-transform active:scale-95">
                <span class="relative z-10 group-hover:text-gold transition-colors">ACCÉDER AU DASHBOARD</span>
                <div class="absolute inset-0 bg-neutral-900 transform scale-y-0 group-hover:scale-y-100 transition-transform origin-bottom duration-300"></div>
            </a>
        </footer>

    </main>

    <script>
        // --- Custom Cursor Logic ---
        const cursor = document.getElementById('cursor'), cursorDot = document.getElementById('cursor-dot');
        const links = document.querySelectorAll('a, .pillar-card');

        document.addEventListener('mousemove', e => { 
            gsap.to(cursor, {x: e.clientX, y: e.clientY, duration: 0.2, ease: 'power2.out'}); 
            gsap.to(cursorDot, {x: e.clientX, y: e.clientY, duration: 0.1, ease: 'power2.out'}); 
        });

        links.forEach(l => { 
            l.addEventListener('mouseenter', () => gsap.to(cursor, {scale: 3, backgroundColor: 'rgba(255,255,255,0.1)', borderColor: 'transparent'})); 
            l.addEventListener('mouseleave', () => gsap.to(cursor, {scale: 1, backgroundColor: 'transparent', borderColor: 'rgba(255,255,255,0.3)'})); 
        });

        // --- Animations ---
        gsap.registerPlugin(ScrollTrigger);

        // Hero Text Split
        const heroText = new SplitType('.split-text', { types: 'lines, words' });
        gsap.from(heroText.words, {
            y: 100, opacity: 0, duration: 1.2, stagger: 0.05, ease: 'power4.out', delay: 0.2
        });
        
        gsap.to('.fade-in', { opacity: 1, duration: 1, delay: 0.8, stagger: 0.2 });

        // Pillar Cards Stagger
        gsap.from('.pillar-card', {
            scrollTrigger: { trigger: '.pillar-card', start: 'top 85%' },
            y: 50, opacity: 0, duration: 1, stagger: 0.2, ease: 'power3.out'
        });

        // Protocol Items
        gsap.utils.toArray('.protocol-item').forEach((item, i) => {
            gsap.from(item, {
                scrollTrigger: { trigger: item, start: 'top 90%' },
                x: -30, opacity: 0, duration: 0.8, ease: 'power2.out', delay: i * 0.1
            });
        });

        // Visual Tiles Animation
        gsap.to('.tile-anim', {
            scrollTrigger: { trigger: '#visual-container', start: 'top 70%' },
            opacity: 1, scale: 1, duration: 0.5, stagger: { amount: 0.5, grid: [3, 3], from: "center" }
        });

        // Quote Parallax
        gsap.from('.quote-anim', {
            scrollTrigger: { trigger: '.quote-anim', start: 'top 85%' },
            scale: 0.95, opacity: 0, duration: 1.5, ease: 'power2.out'
        });

    </script>
</body>
</html>