@extends('layouts.app')

@section('title', 'Modifier Article')
@section('content')

<div class="max-w-2xl mx-auto pb-20 pt-10">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-neutral-900">Modifier Article</h1>
            <p class="text-sm text-neutral-500 mt-1">{{ $article->name }}</p>
        </div>
        <a href="{{ route('tools.logistics.show', $article->bl_id) }}" class="text-xs font-bold uppercase tracking-widest text-neutral-400 hover:text-black">Retour</a>
    </div>

    <form action="{{ route('tools.logistics.article.update_details', $article->id) }}" method="POST" class="bg-white p-8 rounded-3xl shadow-xl border border-neutral-100">
        @csrf @method('PUT')

        <!-- MAIN INFO -->
        <div class="space-y-6">
            
            <!-- Identity -->
            <div class="grid grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">Désignation Produit</label>
                    <input type="text" name="name" value="{{ $article->name }}" class="w-full border-b border-neutral-200 py-2 font-bold text-lg focus:outline-none focus:border-[#E6AF5D]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">Référence</label>
                    <input type="text" name="reference" value="{{ $article->reference }}" class="w-full border-b border-neutral-200 py-2 font-mono text-sm focus:outline-none focus:border-[#E6AF5D]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">Dépôt</label>
                    <select name="warehouse" class="w-full border-b border-neutral-200 py-2 text-sm focus:outline-none focus:border-[#E6AF5D] bg-transparent">
                        <option value="">-- Sélectionner --</option>
                        @foreach(['Mediouna', 'S.M', 'Lkhyayta', 'Diva Ceramica'] as $wh)
                            <option value="{{ $wh }}" {{ $article->warehouse == $wh ? 'selected' : '' }}>{{ $wh }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="border-neutral-100 my-6">

            <!-- LOGISTICS CALCULATOR -->
            <h3 class="font-serif text-lg text-neutral-900 italic mb-4">Logistique & Conditionnement</h3>

            <div class="grid grid-cols-2 gap-6 p-6 bg-neutral-50 rounded-2xl">
                
                <!-- 1. Conversion Base -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[9px] font-bold uppercase text-neutral-400 mb-1">m² par Boîte</label>
                    <div class="relative">
                        <input type="number" step="0.001" name="conversion" id="conversion" value="{{ $conversion }}" 
                            oninput="recalc()"
                            class="w-full bg-white border border-neutral-200 rounded-lg py-2 px-3 text-sm font-bold focus:ring-2 ring-[#E6AF5D]/20 outline-none">
                        <span class="absolute right-3 top-2.5 text-xs text-neutral-400">m²</span>
                    </div>
                </div>

                <!-- 2. Pallet Config -->
                <div class="col-span-2 md:col-span-1">
                    <label class="block text-[9px] font-bold uppercase text-neutral-400 mb-1">Boîtes par Palette</label>
                    <div class="relative">
                        <input type="number" step="1" name="boxes_per_pallet" id="boxes_per_pallet" value="{{ $article->boxes_per_pallet ?? $default_boxes_per_pallet }}" 
                            oninput="recalc()"
                            class="w-full bg-white border border-neutral-200 rounded-lg py-2 px-3 text-sm font-bold focus:ring-2 ring-[#E6AF5D]/20 outline-none">
                        <span class="absolute right-3 top-2.5 text-xs text-neutral-400">crt</span>
                    </div>
                </div>

                <!-- 3. Current Load -->
                <div class="col-span-2 mt-4">
                    <label class="block text-[9px] font-bold uppercase text-neutral-400 mb-1">Quantité Chargée</label>
                    <div class="grid grid-cols-3 gap-4">
                        
                        <!-- Pallets Input -->
                        <div class="relative">
                            <input type="number" step="0.1" name="pallet_count" id="pallet_count" value="{{ $article->pallet_count }}" 
                                oninput="updateFromPallets()"
                                class="w-full bg-white border border-neutral-200 rounded-lg py-2 px-3 text-sm font-bold text-neutral-900 focus:ring-2 ring-black/10 outline-none">
                            <span class="absolute right-3 top-2.5 text-[9px] font-bold uppercase text-neutral-400">Palettes</span>
                        </div>

                        <!-- Boxes Input (Calculated but editable) -->
                        <div class="relative">
                            <input type="number" step="1" id="box_count" 
                                oninput="updateFromBoxes()"
                                class="w-full bg-white border border-neutral-200 rounded-lg py-2 px-3 text-sm font-bold text-neutral-900 focus:ring-2 ring-black/10 outline-none">
                            <span class="absolute right-3 top-2.5 text-[9px] font-bold uppercase text-neutral-400">Boîtes</span>
                        </div>

                        <!-- Total m2 (Final) -->
                        <div class="relative">
                            <input type="number" step="0.01" name="quantity" id="total_qty" value="{{ $article->quantity }}" 
                                class="w-full bg-[#E6AF5D] text-white border-none rounded-lg py-2 px-3 text-sm font-bold focus:outline-none" readonly>
                            <span class="absolute right-3 top-2.5 text-[9px] font-bold uppercase text-white/80">Total m²</span>
                        </div>

                    </div>
                    <p class="text-[10px] text-neutral-400 mt-2 italic text-center">
                        Modifiez les palettes ou boîtes pour recalculer automatiquement le total m².
                    </p>
                </div>

            </div>

        </div>

        <div class="mt-8 flex gap-4">
            <button type="submit" class="flex-1 py-4 bg-black text-white rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-[#E6AF5D] transition-colors shadow-lg">
                Enregistrer Modifications
            </button>
        </div>

    </form>
</div>

<script>
    function recalc() {
        // Just trigger updates based on current priority
        if(document.getElementById('pallet_count').value > 0) updateFromPallets();
        else updateFromBoxes();
    }

    function updateFromPallets() {
        const pallets = parseFloat(document.getElementById('pallet_count').value) || 0;
        const boxesPerPallet = parseFloat(document.getElementById('boxes_per_pallet').value) || 0;
        const conversion = parseFloat(document.getElementById('conversion').value) || 0;

        // Calculate Boxes
        const totalBoxes = Math.round(pallets * boxesPerPallet);
        document.getElementById('box_count').value = totalBoxes;

        // Calculate m2
        const totalM2 = totalBoxes * conversion;
        document.getElementById('total_qty').value = totalM2.toFixed(2);
    }

    function updateFromBoxes() {
        const boxes = parseFloat(document.getElementById('box_count').value) || 0;
        const boxesPerPallet = parseFloat(document.getElementById('boxes_per_pallet').value) || 1; // Avoid div by 0
        const conversion = parseFloat(document.getElementById('conversion').value) || 0;

        // Calculate Pallets
        const pallets = boxes / boxesPerPallet;
        document.getElementById('pallet_count').value = pallets.toFixed(2);

        // Calculate m2
        const totalM2 = boxes * conversion;
        document.getElementById('total_qty').value = totalM2.toFixed(2);
    }

    // Init logic on load
    window.addEventListener('load', () => {
        // Reverse engineer box count from total quantity if possible
        const totalM2 = parseFloat(document.getElementById('total_qty').value) || 0;
        const conversion = parseFloat(document.getElementById('conversion').value) || 0;
        
        if(conversion > 0 && totalM2 > 0) {
            const boxes = Math.round(totalM2 / conversion);
            document.getElementById('box_count').value = boxes;
        }
    });
</script>

@endsection
