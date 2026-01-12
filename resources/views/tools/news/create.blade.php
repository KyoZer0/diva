@extends('layouts.app')

@section('title', 'Publier Nouveauté')
@section('content')

<div class="max-w-2xl mx-auto mt-10">
    <div class="bg-white p-8 rounded-3xl border border-neutral-200 shadow-lg">
        <h1 class="text-2xl font-serif font-bold text-neutral-900 mb-6">Ajouter une Référence</h1>
        
        <form action="{{ route('tools.news.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Nom du Produit</label>
                <input type="text" name="title" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-[#E6AF5D]" placeholder="Ex: Marbre Calacatta Gold" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-[#E6AF5D]" placeholder="Détails sur la finition, l'origine..."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Stock Arrivé</label>
                    <input type="number" name="stock_quantity" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3 focus:outline-none focus:border-[#E6AF5D]" placeholder="0" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">Unité</label>
                    <select name="unit" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3">
                        <option value="m2">m²</option>
                        <option value="pcs">Pièces</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-neutral-400 uppercase mb-2">URL Image (Optionnel)</label>
                <input type="url" name="image_url" class="w-full bg-neutral-50 border border-neutral-200 rounded-xl p-3 text-xs" placeholder="https://...">
                <p class="text-[10px] text-gray-400 mt-1">Laissez vide pour l'image par défaut.</p>
            </div>

            <button type="submit" class="w-full py-4 bg-black text-white font-bold rounded-xl text-xs uppercase tracking-widest hover:bg-[#E6AF5D] hover:text-black transition-all">
                Publier l'Arrivage
            </button>
        </form>
    </div>
</div>
@endsection