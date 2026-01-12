@extends('layouts.app')

@section('title', 'Modifier le BL')
@section('content')

<div class="max-w-5xl mx-auto pb-20">
    
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-serif text-neutral-900">Modification Dossier</h1>
        <a href="{{ route('tools.logistics.show', $bl->id) }}" class="text-xs font-bold uppercase tracking-widest text-neutral-400 hover:text-black">Annuler</a>
    </div>

    <form action="{{ route('tools.logistics.update', $bl->id) }}" method="POST" class="space-y-12">
        @csrf @method('PUT')

        <!-- SECTION 1: DETAILS -->
        <div class="bg-white p-8 border border-neutral-200">
            <h3 class="font-bold text-xs uppercase tracking-widest text-neutral-400 mb-6 border-b border-neutral-100 pb-2">Informations Générales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1">Numéro BL</label>
                    <input type="text" name="bl_number" value="{{ $bl->bl_number }}" class="w-full border-b border-neutral-300 py-2 focus:border-black focus:ring-0 font-mono" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1">Client</label>
                    <input type="text" name="client_name" value="{{ $bl->client_name }}" class="w-full border-b border-neutral-300 py-2 focus:border-black focus:ring-0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1">Date</label>
                    <input type="date" name="date" value="{{ $bl->date }}" class="w-full border-b border-neutral-300 py-2 focus:border-black focus:ring-0" required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                 <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1">Fournisseur</label>
                    <input type="text" name="supplier_name" value="{{ $bl->supplier_name }}" class="w-full border-b border-neutral-300 py-2 focus:border-black focus:ring-0">
                </div>
                 <div>
                    <label class="block text-xs font-bold text-neutral-500 mb-1">Réf Fournisseur</label>
                    <input type="text" name="supplier_ref" value="{{ $bl->supplier_ref }}" class="w-full border-b border-neutral-300 py-2 focus:border-black focus:ring-0">
                </div>
            </div>
        </div>

        <!-- SECTION 2: ARTICLES -->
        <div class="bg-white p-8 border border-neutral-200">
             <div class="flex justify-between items-end mb-6 border-b border-neutral-100 pb-2">
                <h3 class="font-bold text-xs uppercase tracking-widest text-neutral-400">Articles</h3>
                <button type="button" onclick="addRow()" class="text-xs font-bold text-[#E6AF5D] hover:text-black uppercase tracking-widest">+ Ajouter Ligne</button>
            </div>

            <div id="itemsContainer" class="space-y-4">
                @foreach($bl->articles as $index => $article)
                <div class="grid grid-cols-12 gap-4 items-center group">
                    <!-- HIDDEN ID to track updates -->
                    <input type="hidden" name="articles[{{ $index }}][id]" value="{{ $article->id }}">
                    
                    <div class="col-span-2">
                         <input type="text" name="articles[{{ $index }}][reference]" value="{{ $article->reference }}" class="w-full bg-neutral-50 text-xs p-2 font-mono" placeholder="REF">
                    </div>
                    <div class="col-span-6">
                         <input type="text" name="articles[{{ $index }}][name]" value="{{ $article->name }}" class="w-full border-b border-neutral-200 py-1 text-sm font-bold" placeholder="Nom">
                    </div>
                    <div class="col-span-2">
                         <input type="number" name="articles[{{ $index }}][quantity]" value="{{ $article->quantity }}" step="0.01" class="w-full border-b border-neutral-200 py-1 text-center font-serif">
                    </div>
                    <div class="col-span-1">
                         <select name="articles[{{ $index }}][unit]" class="w-full text-xs border-none bg-transparent">
                            <option value="m2" {{ $article->unit == 'm2' ? 'selected' : '' }}>m²</option>
                            <option value="box" {{ $article->unit == 'box' ? 'selected' : '' }}>box</option>
                            <option value="pcs" {{ $article->unit == 'pcs' ? 'selected' : '' }}>pcs</option>
                         </select>
                    </div>
                    <div class="col-span-1 text-right">
                        <!-- If user removes row, we remove DOM. Logic in controller handles delete by absence of ID -->
                        <button type="button" onclick="this.closest('.grid').remove()" class="text-neutral-300 hover:text-red-500">✕</button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-right">
            <button type="submit" class="px-8 py-4 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-[#E6AF5D] transition-all">Enregistrer Modifications</button>
        </div>

    </form>
</div>

<script>
    let rowIndex = {{ $bl->articles->count() }};
    const container = document.getElementById('itemsContainer');

    function addRow() {
        const div = document.createElement('div');
        div.className = 'grid grid-cols-12 gap-4 items-center group animate-fade-in';
        div.innerHTML = `
            <div class="col-span-2">
                    <input type="text" name="articles[${rowIndex}][reference]" class="w-full bg-neutral-50 text-xs p-2 font-mono" placeholder="REF">
            </div>
            <div class="col-span-6">
                    <input type="text" name="articles[${rowIndex}][name]" class="w-full border-b border-neutral-200 py-1 text-sm font-bold" placeholder="Nom produit" required>
            </div>
            <div class="col-span-2">
                    <input type="number" name="articles[${rowIndex}][quantity]" step="0.01" class="w-full border-b border-neutral-200 py-1 text-center font-serif" placeholder="0">
            </div>
            <div class="col-span-1">
                    <select name="articles[${rowIndex}][unit]" class="w-full text-xs border-none bg-transparent">
                    <option value="m2">m²</option>
                    <option value="box">box</option>
                    <option value="pcs">pcs</option>
                    </select>
            </div>
            <div class="col-span-1 text-right">
                <button type="button" onclick="this.closest('.grid').remove()" class="text-neutral-300 hover:text-red-500">✕</button>
            </div>
        `;
        container.appendChild(div);
        rowIndex++;
    }
</script>
@endsection