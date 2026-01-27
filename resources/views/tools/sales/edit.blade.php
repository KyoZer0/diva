@extends('layouts.app')

@section('title', 'Modifier ' . $client->name)

@section('content')
<div class="max-w-xl mx-auto space-y-8">

    <a href="{{ route('tools.sales.show', $client->id) }}" class="inline-flex items-center text-sm font-bold text-neutral-400 hover:text-neutral-900 mb-8 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au Dossier
    </a>

    <div class="bg-white rounded-[2rem] p-8 border border-neutral-100 shadow-sm">
        <h1 class="text-2xl font-serif font-bold text-neutral-900 mb-6">Modifier le Dossier</h1>
        
        <form action="{{ route('tools.sales.update', $client->id) }}" method="POST" class="space-y-5" x-data="{ type: '{{ $client->client_type }}' }">
            @csrf
            @method('PUT')

            <!-- Type Selection -->
            <div class="grid grid-cols-2 gap-2 p-1 bg-neutral-100 rounded-xl">
                <label class="cursor-pointer text-center text-sm font-bold py-2 rounded-lg transition-all" 
                       :class="type === 'particulier' ? 'bg-white shadow text-neutral-900' : 'text-neutral-500 hover:text-neutral-700'">
                    <input type="radio" name="client_type" value="particulier" class="hidden" x-model="type">
                    Particulier
                </label>
                <label class="cursor-pointer text-center text-sm font-bold py-2 rounded-lg transition-all"
                       :class="type === 'professionnel' ? 'bg-white shadow text-neutral-900' : 'text-neutral-500 hover:text-neutral-700'">
                    <input type="radio" name="client_type" value="professionnel" class="hidden" x-model="type">
                    Professionnel
                </label>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Nom Complet</label>
                    <input type="text" name="name" value="{{ $client->full_name }}" required class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D]">
                </div>

                <div x-show="type === 'professionnel'" x-transition class="space-y-4">
                     <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Société</label>
                        <input type="text" name="company_name" value="{{ $client->company_name }}" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D]">
                     </div>
                     
                     <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Catégorie</label>
                        <select name="professional_category" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] text-neutral-700 appearance-none">
                            <option value="" disabled {{ !$client->professional_category ? 'selected' : '' }}>Choisir une catégorie</option>
                            <option value="revendeur" {{ $client->professional_category == 'revendeur' ? 'selected' : '' }}>Revendeur</option>
                            <option value="architecte" {{ $client->professional_category == 'architecte' ? 'selected' : '' }}>Architecte</option>
                            <option value="promoteur" {{ $client->professional_category == 'promoteur' ? 'selected' : '' }}>Promoteur</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none mt-6">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                     </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                         <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Téléphone</label>
                         <input type="text" name="phone" value="{{ $client->phone }}" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D]">
                    </div>
                    <div>
                         <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Email</label>
                         <input type="email" name="email" value="{{ $client->email }}" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-neutral-500 mb-2">Tags d'Intérêt (séparés par virgule)</label>
                    <input type="text" name="interest_tags" value="{{ implode(', ', $client->products ?? []) }}" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D]">
                </div>
            </div>

            <div class="pt-4 flex gap-4">
                <a href="{{ route('tools.sales.show', $client->id) }}" class="w-full bg-neutral-100 text-neutral-600 font-bold py-4 rounded-xl hover:bg-neutral-200 transition-colors text-center">
                    Annuler
                </a>
                <button type="submit" class="w-full bg-neutral-900 text-white font-bold py-4 rounded-xl hover:bg-black transition-colors">
                    Sauvegarder
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
