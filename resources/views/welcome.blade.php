<!DOCTYPE html>
<html lang="fr" class="antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Diva Ceramica | Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { gold: '#E6AF5D', dark: '#050505' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600&display=swap');
        body { background-color: #050505; color: white; overflow: hidden; cursor: none; }
        #cursor { position: fixed; top: 0; left: 0; width: 20px; height: 20px; border: 1px solid rgba(255, 255, 255, 0.5); border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); transition: width 0.3s, height 0.3s, background-color 0.3s; mix-blend-mode: difference; }
        #cursor-dot { position: fixed; top: 0; left: 0; width: 4px; height: 4px; background-color: #E6AF5D; border-radius: 50%; pointer-events: none; z-index: 9999; transform: translate(-50%, -50%); }
        .ambient-light { position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(230,175,93,0.15) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; pointer-events: none; z-index: 0; filter: blur(40px); }
        .noise { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1; opacity: 0.05; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
        .reveal-text { clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%); }
    </style>
</head>
<body class="selection:bg-gold selection:text-black">
    <div id="cursor"></div><div id="cursor-dot"></div>
    <div class="ambient-light" id="ambient"></div>
    <div class="noise"></div>
    <div class="fixed inset-0 z-0 pointer-events-none opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 100px 100px;"></div>

    <main class="relative z-10 flex flex-col justify-between min-h-screen px-8 py-8 md:px-16 md:py-12">
        <header class="flex justify-between items-center opacity-0" id="header">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-gold rounded-full animate-pulse"></div>
                <span class="text-sm font-medium tracking-widest uppercase text-neutral-400">Diva Ceramica</span>
            </div>
            <div class="text-xs font-mono text-neutral-600">INTERNAL SYS. V.2.0</div>
        </header>

        <div class="flex flex-col items-start justify-center flex-1 max-w-5xl mx-auto w-full">
            <div class="space-y-2 mb-12">
                <div class="reveal-text overflow-hidden"><h1 class="hero-line text-5xl md:text-7xl lg:text-8xl font-light tracking-tight text-white leading-tight">L'Art de la</h1></div>
                <div class="reveal-text overflow-hidden"><h1 class="hero-line text-5xl md:text-7xl lg:text-8xl font-light tracking-tight text-white leading-tight">Relation <span class="text-gold font-serif italic">Client</span></h1></div>
            </div>
            <div class="max-w-md overflow-hidden mb-12">
                <p class="desc-text text-neutral-400 text-sm md:text-base leading-relaxed opacity-0 translate-y-4">
                    Plateforme de gestion interne. Suivi commercial, analytique avancée et performance en temps réel. Accès réservé.
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto overflow-hidden">
                <a href="{{ route('login') }}" class="btn-anim group relative px-8 py-4 bg-white text-black rounded-full overflow-hidden flex items-center justify-center gap-3 opacity-0 translate-y-4">
                    <span class="relative z-10 font-bold text-sm tracking-wide group-hover:text-gold transition-colors duration-300">CONNEXION</span>
                    <svg class="relative z-10 w-4 h-4 group-hover:translate-x-1 group-hover:text-gold transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    <div class="absolute inset-0 bg-neutral-900 transform scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-500 ease-out"></div>
                </a>
                <a href="{{ route('documentation') }}" class="btn-anim group relative px-8 py-4 border border-neutral-800 text-white rounded-full overflow-hidden flex items-center justify-center opacity-0 translate-y-4">
                    <span class="relative z-10 font-medium text-sm tracking-wide group-hover:text-black transition-colors duration-300">PROTOCOLE & AIDE</span>
                    <div class="absolute inset-0 bg-white transform scale-y-0 group-hover:scale-y-100 transition-transform origin-bottom duration-500 ease-out"></div>
                </a>
            </div>
        </div>

        <footer class="flex justify-between items-center opacity-0" id="footer">
            <p class="text-[10px] text-neutral-700">&copy; {{ date('Y') }} Diva Ceramica. Accès Restreint.</p>
        </footer>
    </main>

    <script>
        const cursor = document.getElementById('cursor'), cursorDot = document.getElementById('cursor-dot');
        document.addEventListener('mousemove', e => { gsap.to(cursor, {x: e.clientX, y: e.clientY, duration: 0.2}); gsap.to(cursorDot, {x: e.clientX, y: e.clientY, duration: 0.1}); });
        document.querySelectorAll('a').forEach(l => { l.addEventListener('mouseenter', () => gsap.to(cursor, {scale: 3, backgroundColor: 'rgba(255,255,255,0.1)', borderColor: 'transparent'})); l.addEventListener('mouseleave', () => gsap.to(cursor, {scale: 1, backgroundColor: 'transparent', borderColor: 'rgba(255,255,255,0.5)'})); });
        document.addEventListener('mousemove', e => gsap.to('#ambient', {x: e.clientX - 300, y: e.clientY - 300, duration: 1.5}));
        
        const tl = gsap.timeline({defaults: {ease: 'power3.out'}});
        tl.to('#header', {opacity: 1, duration: 1, delay: 0.2})
          .from('.hero-line', {y: 150, opacity: 0, duration: 1.2, stagger: 0.15, skewY: 5}, "-=0.5")
          .to('.desc-text', {y: 0, opacity: 1, duration: 1}, "-=0.8")
          .to('.btn-anim', {y: 0, opacity: 1, duration: 0.8, stagger: 0.1}, "-=0.6")
          .to('#footer', {opacity: 1, duration: 1}, "-=0.5");
    </script>
</body>
</html>