@extends('layouts.app')

@section('title', $bl->bl_number)
@section('content')

<div class="max-w-7xl mx-auto pb-20">

    <!-- HERO HEADER -->
    <div class="border-b border-neutral-200 pb-8 mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('tools.logistics.index') }}" class="text-xs font-bold text-neutral-400 hover:text-black uppercase tracking-widest">Dashboard</a>
                <span class="text-neutral-300">/</span>
                <span class="text-xs font-bold text-[#E6AF5D] uppercase tracking-widest">Détail</span>
            </div>
            <h1 class="text-5xl font-serif text-neutral-900">{{ $bl->client_name }}</h1>
            <div class="flex items-center gap-4 mt-4 font-mono text-sm text-neutral-500">
                <span class="bg-neutral-100 px-2 py-1 rounded">{{ $bl->bl_number }}</span>
                @if($bl->supplier_ref)
                <span class="bg-neutral-50 text-neutral-400 px-2 py-1 rounded border border-neutral-100" title="BL Fournisseur">Ref: {{ $bl->supplier_ref }}</span>
                @endif
                <span>{{ \Carbon\Carbon::parse($bl->date)->format('d/m/Y') }}</span>
                @if($bl->supplier_name)
                <span class="text-[#E6AF5D]">• {{ $bl->supplier_name }}</span>
                @endif
            </div>
        </div>
        
        <div class="flex gap-3">

            
             <a href="{{ route('tools.logistics.edit', $bl->id) }}" class="px-6 py-3 border border-neutral-200 text-neutral-900 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-black hover:text-white transition-all">
                Modifier
            </a>
            
            <form action="{{ route('tools.logistics.updateStatus', $bl->id) }}" method="POST">
                @csrf @method('PUT')
                @if($bl->status != 'delivered')
                <button type="submit" name="status" value="delivered" onclick="return confirm('Tout valider ?')" class="px-6 py-3 bg-black text-white rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-[#E6AF5D] hover:text-black transition-all shadow-lg">
                    Valider Chargement
                </button>
                @else
                <button type="button" class="px-6 py-3 bg-emerald-50 text-emerald-800 border border-emerald-100 rounded-lg text-xs font-bold uppercase tracking-widest cursor-default">
                    ✓ Terminé
                </button>
                @endif
            </form>
            
            <!-- DELETE FORM -->
            <form action="{{ route('tools.logistics.destroy', $bl->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier définitivement ?');">
                @csrf @method('DELETE')
                <button type="submit" class="px-4 py-3 border border-red-200 text-red-600 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        
        <!-- LEFT: THE MANIFEST (Paper Style) -->
        <div class="lg:col-span-8">
            <div class="bg-white shadow-xl shadow-neutral-100 border border-neutral-100 min-h-[600px] relative">
                <!-- Paper texture overlay -->
                <div class="absolute inset-0 pointer-events-none opacity-50" style="background-image: radial-gradient(#000 0.5px, transparent 0); background-size: 20px 20px;"></div>
                
                <div class="relative z-10 p-8">
                    <h3 class="font-serif text-xl italic text-neutral-900 mb-6 border-b-2 border-black pb-2">Manifeste de Chargement</h3>
                    
                    <div class="space-y-1">
                        <!-- HEADER -->
                        <div class="grid grid-cols-12 gap-4 text-[10px] font-bold uppercase text-neutral-400 tracking-widest pb-4 pl-4">
                            <div class="col-span-2">Ref</div>
                            <div class="col-span-6">Article</div>
                            <div class="col-span-2 text-center">Qté</div>
                            <div class="col-span-2 text-right">Statut</div>
                        </div>

                        <!-- ITEMS -->
                        @foreach($bl->articles as $article)
                        <div class="grid grid-cols-12 gap-4 items-center py-4 px-4 border-b border-neutral-100 hover:bg-neutral-50 transition-colors group {{ $article->status == 'delivered' ? 'bg-neutral-50/50' : '' }}">
                            
                            <!-- Ref -->
                            <div class="col-span-2 font-mono text-xs text-neutral-500">
                                {{ $article->reference ?? '--' }}
                            </div>

                            <!-- Name -->
                            <div class="col-span-6">
                                <a href="{{ route('tools.logistics.article.edit_details', $article->id) }}" class="group/link block">
                                    <span class="font-bold text-neutral-900 text-sm group-hover/link:text-[#E6AF5D] transition-colors {{ $article->status == 'delivered' ? 'text-neutral-400 line-through decoration-emerald-500' : '' }}">
                                        {{ $article->name }} ✎
                                    </span>
                                </a>
                                @if($article->warehouse)
                                <div class="text-[9px] text-neutral-400 mt-0.5 flex items-center gap-2">
                                    <span class="bg-neutral-100 px-1.5 py-0.5 rounded text-neutral-500 font-medium uppercase tracking-wider">{{ $article->warehouse }}</span>
                                    @if($article->pallet_count)
                                    <span class="text-neutral-300">•</span>
                                    <span>{{ $article->pallet_count }} Palettes</span>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <!-- Qty -->
                            <div class="col-span-2 text-center">
                                <span class="font-serif text-lg {{ $article->status == 'delivered' ? 'text-emerald-600' : 'text-neutral-900' }}">
                                    {{ floatval($article->quantity) }}
                                </span>
                                <span class="text-[9px] text-neutral-400 uppercase">{{ $article->unit }}</span>
                                
                                @if($article->status == 'partial')
                                <div class="text-[9px] font-bold text-amber-600 mt-1">
                                    {{ floatval($article->quantity_delivered) }} Chargé
                                </div>
                                @endif
                            </div>

                            <!-- Action / Status -->
                            <div class="col-span-2 text-right">
                                <form action="{{ route('tools.logistics.article.update', $article->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    
                                    @if($article->status == 'pending')
                                        <button type="submit" name="status" value="delivered" class="w-8 h-8 rounded-full border border-neutral-300 text-neutral-300 hover:border-black hover:bg-black hover:text-white flex items-center justify-center transition-all ml-auto">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @elseif($article->status == 'delivered')
                                        <button type="submit" name="status" value="pending" class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center ml-auto hover:bg-red-500 transition-colors" title="Annuler">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    @else
                                        <!-- Partial State -->
                                        <button type="button" onclick="openPartialModal('{{ $article->id }}', '{{ $article->name }}', '{{ $article->quantity }}')" class="w-auto px-2 py-1 bg-amber-100 text-amber-700 text-[9px] font-bold uppercase rounded ml-auto hover:bg-amber-200">
                                            Partiel
                                        </button>
                                    @endif
                                </form>
                                <!-- Hidden trigger for partial -->
                                @if($article->status != 'delivered')
                                <button type="button" onclick="openPartialModal('{{ $article->id }}', '{{ $article->name }}', '{{ $article->quantity }}')" class="text-[9px] text-neutral-300 underline mt-1 hover:text-amber-500">
                                    Option Partiel
                                </button>
                                @endif
                            </div>

                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: CONTEXT -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- Notes -->
            <div>
                <h4 class="font-serif text-lg text-neutral-900 mb-4 italic">Notes & Incidents</h4>
                <div class="bg-[#F9F9F9] p-6 border-l-2 border-black">
                    <form action="{{ route('tools.logistics.note', $bl->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="relative">
                            <input type="text" name="note" class="w-full bg-transparent border-b border-neutral-300 py-2 text-sm focus:border-[#E6AF5D] focus:ring-0 placeholder-neutral-400" placeholder="Écrire une observation..." required>
                            <button type="submit" class="absolute right-0 top-2 text-neutral-400 hover:text-black">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </form>

                    <div class="space-y-4 max-h-60 overflow-y-auto">
                        @foreach($bl->history as $log)
                        <div class="text-xs">
                            <p class="text-neutral-800">{{ $log->details }}</p>
                            <span class="text-[10px] text-neutral-400 font-mono">{{ $log->created_at->format('d/m H:i') }} • {{ $log->user->name ?? 'System' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Scan Preview -->
            @if($bl->supplier_photo)
            <div>
                <h4 class="font-serif text-lg text-neutral-900 mb-4 italic">Document Source</h4>
                <a href="{{ asset('storage/'.$bl->supplier_photo) }}" target="_blank" class="block overflow-hidden rounded-lg border border-neutral-200 group relative">
                    <img src="{{ asset('storage/'.$bl->supplier_photo) }}" class="w-full h-auto opacity-80 group-hover:opacity-100 transition-opacity grayscale group-hover:grayscale-0">
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20">
                        <span class="bg-white px-3 py-1 text-xs font-bold uppercase tracking-widest">Voir</span>
                    </div>
                </a>
            </div>
            @endif

        </div>
    </div>
</div>

<!-- Modal Logic for Partial (Same as before) -->
<div id="partialModal" class="hidden fixed inset-0 bg-neutral-900/90 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-sm p-8 shadow-2xl relative">
        <button onclick="document.getElementById('partialModal').classList.add('hidden')" class="absolute top-4 right-4 text-neutral-400 hover:text-black">✕</button>
        <h3 class="font-serif text-2xl mb-1">Chargement Partiel</h3>
        <p class="text-xs font-mono text-neutral-400 uppercase tracking-widest mb-8" id="partialItemName">...</p>
        <form method="POST" id="partialForm">
            @csrf @method('PUT')
            <input type="hidden" name="status" value="partial">
            <input type="number" name="quantity_delivered" class="w-full border-b border-black text-3xl font-serif py-2 focus:outline-none mb-6" placeholder="0" step="0.01" autofocus>
            <button type="submit" class="w-full py-4 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-[#E6AF5D] transition-colors">Confirmer</button>
        </form>
    </div>
</div>
<script>
    function openPartialModal(id, name, max) {
        document.getElementById('partialModal').classList.remove('hidden');
        document.getElementById('partialForm').action = `/tools/logistics/article/${id}`;
        document.getElementById('partialItemName').innerText = name + ` (Max: ${max})`;
    }
</script>
@endsection