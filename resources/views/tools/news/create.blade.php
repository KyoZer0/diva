@extends('layouts.app')

@section('title', 'Publier une Nouveauté')

@section('content')
<div class="max-w-2xl mx-auto py-12">
    <a href="{{ route('tools.sales.news') }}" class="inline-flex items-center text-sm font-bold text-neutral-400 hover:text-neutral-900 mb-8">
        ← Retour aux Nouveautés
    </a>

    <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-lg">
        <h1 class="text-3xl font-serif font-bold text-neutral-900 mb-8">Publier une Annonce</h1>

        <form action="{{ route('admin.news.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Titre</label>
                <input type="text" name="title" required class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]">
            </div>

            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Image URL (Optionnel)</label>
                <input type="url" name="image_url" placeholder="https://..." class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-neutral-700 mb-2">Stock Initial</label>
                    <input type="number" name="stock_quantity" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]">
                </div>
                <div>
                     <label class="block text-sm font-bold text-neutral-700 mb-2">Unité</label>
                     <select name="unit" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]">
                        <option value="m2">m²</option>
                        <option value="pcs">Pièces</option>
                        <option value="box">Boîtes</option>
                     </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-neutral-700 mb-2">Dépôt</label>
                <select name="warehouse" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#E6AF5D]">
                    <option value="">Non spécifié</option>
                    <option value="Mediouna">Mediouna</option>
                    <option value="S.M">S.M</option>
                    <option value="Lkhyayta">Lkhyayta</option>
                    <option value="Diva Ceramica">Diva Ceramica</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-[#E6AF5D] hover:text-black transition-all shadow-lg mt-4">
                Publier
            </button>
        </form>
    </div>
</div>
@endsection