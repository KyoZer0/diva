@extends('layouts.app')
@section('title', 'Clôture Journalière')
@section('content')

<!-- Load GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<div class="fixed inset-0 bg-neutral-900 z-50 flex flex-col overflow-hidden">
    
    <!-- Top Bar -->
    <div class="flex justify-between items-center p-8 text-white relative z-50">
        <div>
            <h1 class="font-serif text-3xl mb-1">Clôture Journalière</h1>
            <p class="text-neutral-400 text-sm">Récapitulatif du {{ now()->format('d/m/Y') }}</p>
        </div>
        <div class="text-right">
            <div class="text-4xl font-bold text-[#E6AF5D]">{{ $bls->count() }}</div>
            <div class="text-[10px] uppercase tracking-widest text-neutral-500">Dossiers Totaux</div>
        </div>
    </div>

    <!-- The Stage -->
    <div class="flex-1 relative flex items-center justify-center perspective-1000" id="cardStage">
        
        @foreach($bls as $index => $bl)
        <div class="bl-card absolute w-[90%] md:w-[500px] bg-white rounded-2xl p-8 shadow-2xl border-t-4 {{ $bl->status == 'delivered' ? 'border-emerald-500' : 'border-amber-500' }}" style="z-index: {{ 100 - $index }}">
            
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-4xl font-serif font-bold text-neutral-900 mb-2">{{ $bl->bl_number }}</h2>
                    <div class="text-sm font-bold text-neutral-500">{{ $bl->client_name }}</div>
                </div>
                <div class="flex flex-col items-end">
                    @if($bl->status == 'delivered')
                        <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded text-xs font-bold uppercase">Terminé</span>
                    @else
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded text-xs font-bold uppercase animate-pulse">Attention: {{ ucfirst($bl->status) }}</span>
                    @endif
                </div>
            </div>

            <div class="bg-neutral-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between text-xs text-neutral-400 uppercase font-bold mb-2">
                    <span>Progression</span>
                    <span>{{ $bl->articles->where('status', 'delivered')->count() }} / {{ $bl->articles->count() }} art.</span>
                </div>
                <div class="w-full bg-neutral-200 h-2 rounded-full overflow-hidden">
                    <div class="h-full bg-neutral-900" style="width: {{ ($bl->articles->count() > 0 ? ($bl->articles->where('status', 'delivered')->count() / $bl->articles->count()) * 100 : 0) }}%"></div>
                </div>
            </div>

            <div class="flex gap-4">
                <button onclick="nextCard()" class="flex-1 py-4 bg-black text-white rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-[#E6AF5D] hover:text-black transition-colors">
                    Vérifié
                </button>
                <a href="{{ route('tools.logistics.show', $bl->id) }}" target="_blank" class="px-4 py-4 border border-neutral-200 rounded-xl hover:bg-neutral-50 text-neutral-400 hover:text-neutral-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
        @endforeach

        <!-- Final "Done" Card -->
        <div id="finalCard" class="absolute w-[90%] md:w-[500px] bg-[#E6AF5D] rounded-2xl p-12 shadow-2xl text-center transform scale-90 opacity-0 pointer-events-none">
            <h2 class="text-4xl font-serif font-bold text-black mb-4">Journée Terminée</h2>
            <p class="text-black/80 mb-8 font-medium">Tous les dossiers ont été passés en revue avec succès.</p>
            <a href="{{ route('tools.logistics.index') }}" class="inline-block px-8 py-4 bg-black text-white rounded-xl font-bold uppercase tracking-widest hover:scale-105 transition-transform">
                Retour Dashboard
            </a>
        </div>

    </div>

</div>

<script>
    // GSAP Setup
    const cards = document.querySelectorAll('.bl-card');
    const finalCard = document.getElementById('finalCard');
    let currentCardIndex = 0;

    // Initial Stack Animation
    if(cards.length > 0) {
        gsap.from(cards, {
            y: 500,
            opacity: 0,
            rotation: 5,
            stagger: 0.1,
            duration: 1,
            ease: "power3.out"
        });
    } else {
        showFinal(); // No cards today? Show finish immediately
    }

    function nextCard() {
        if (currentCardIndex < cards.length) {
            // Throw current card away
            gsap.to(cards[currentCardIndex], {
                x: 500,
                y: -100,
                rotation: 45,
                opacity: 0,
                duration: 0.6,
                ease: "power2.in"
            });
            
            // Bring next card forward (if exists)
            if (cards[currentCardIndex + 1]) {
                gsap.to(cards[currentCardIndex + 1], {
                    scale: 1,
                    y: 0,
                    duration: 0.4
                });
            }

            currentCardIndex++;

            // If checked all
            if (currentCardIndex === cards.length) {
                setTimeout(showFinal, 300);
            }
        }
    }

    function showFinal() {
        gsap.to(finalCard, {
            scale: 1,
            opacity: 1,
            pointerEvents: 'all',
            duration: 0.8,
            ease: "elastic.out(1, 0.5)",
            delay: 0.2
        });
    }
</script>
@endsection