@extends('layouts.app')

@section('title', 'Cockpit Commercial')

@section('content')
<div class="max-w-7xl mx-auto space-y-10">

    <!-- HEADER / NAV -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 bg-white rounded-3xl p-8 border border-neutral-100 shadow-sm relative overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-[#E6AF5D] rounded-full blur-[100px] opacity-10 pointer-events-none -mr-20 -mt-20"></div>

        <div class="relative z-10">
            <h1 class="text-4xl font-serif font-bold text-neutral-900 mb-2">Bonjour, {{ Auth::user()->name }}.</h1>
            <p class="text-neutral-500 max-w-lg">
                Prêt à développer votre portefeuille aujourd'hui ?
            </p>
        </div>
    </div>

    <!-- QUICK ACTIONS / APPS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- APP 1: PERFORMANCE -->
        <a href="{{ route('tools.sales.performance') }}" class="group relative bg-neutral-900 rounded-3xl p-6 overflow-hidden hover:scale-[1.01] transition-transform">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#E6AF5D] rounded-full blur-3xl opacity-20 -mr-10 -mt-10 group-hover:opacity-30 transition-opacity"></div>
            <div class="relative z-10 flex justify-between items-end">
                <div>
                    <h3 class="text-white font-bold text-lg mb-1">Ma Performance</h3>
                    <p class="text-neutral-500 text-xs">Analyses et objectifs</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-[#E6AF5D]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </a>

        <!-- APP 2: AGENDA (Manual Tasks) -->
        <a href="{{ route('tools.sales.agenda') }}" class="group relative bg-white border border-neutral-100 rounded-3xl p-6 overflow-hidden hover:border-[#E6AF5D] transition-colors">
            <div class="relative z-10 flex justify-between items-end">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-neutral-900 font-bold text-lg">Stratégie & Notes</h3>
                        @if($taskCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-[10px] font-bold">{{ $taskCount }}</span>
                        @endif
                    </div>
                    <p class="text-neutral-500 text-xs">Vos tâches et mémos</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-neutral-50 flex items-center justify-center text-neutral-900 group-hover:bg-[#E6AF5D] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
            </div>
        </a>

        <!-- APP 3: NEWS -->
        <a href="{{ route('tools.sales.news') }}" class="group relative bg-white border border-neutral-100 rounded-3xl p-6 overflow-hidden hover:border-[#E6AF5D] transition-colors">
            <div class="relative z-10 flex justify-between items-end">
                <div>
                    <h3 class="text-neutral-900 font-bold text-lg mb-1">Nouveautés</h3>
                    <p class="text-neutral-500 text-xs">Arrivages et annonces</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-neutral-50 flex items-center justify-center text-neutral-900 group-hover:bg-[#E6AF5D] group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
            </div>
        </a>
    </div>

    <!-- PORTFOLIO GRID (Full Width since Feed is removed) -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-lg text-neutral-900">Portefeuille Clients</h2>
            <button onclick="document.getElementById('add-client-modal').showModal()" class="bg-neutral-900 text-white px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors">
                + Nouveau
            </button>
        </div>

        <!-- PORTFOLIO CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myClients as $client)
            <div class="bg-white rounded-2xl p-6 border border-neutral-100 shadow-sm hover:shadow-md transition-shadow relative group">
                <a href="{{ route('tools.sales.show', $client->id) }}" class="absolute inset-0 z-10"></a>
                
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center font-serif text-neutral-500 font-bold">
                            {{ substr($client->full_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-neutral-900 text-sm truncate max-w-[150px]">{{ $client->display_name }}</h3>
                            @if($client->professional_category)
                                <span class="text-[10px] bg-neutral-100 px-1.5 py-0.5 rounded text-neutral-500 uppercase tracking-wide">{{ $client->professional_category }}</span>
                            @elseif($client->company_name)
                                <p class="text-xs text-neutral-400 truncate max-w-[150px]">{{ $client->company_name }}</p>
                            @endif
                        </div>
                    </div>
                    <!-- Status Pill -->
                        @php
                        $colors = [
                            'cold' => 'bg-neutral-100 text-neutral-500',
                            'warm' => 'bg-orange-50 text-orange-600',
                            'hot' => 'bg-red-50 text-red-600',
                            'waiting_stock' => 'bg-blue-50 text-blue-600'
                        ];
                    @endphp
                    <span class="w-2 h-2 rounded-full {{ $colors[$client->smart_status] ?? 'bg-gray-200' }}"></span>
                </div>

                <!-- Mini Stats -->
                <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-neutral-50">
                    <div>
                        <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Potentiel</span>
                        <div class="w-full bg-neutral-100 h-1.5 rounded-full mt-1">
                            <div class="bg-[#E6AF5D] h-1.5 rounded-full" style="width: {{ $client->potential_score }}%"></div>
                        </div>
                    </div>
                    <div class="text-right">
                            <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Dernier Contact</span>
                            <span class="text-xs font-bold text-neutral-700">
                                {{ $client->last_contact ? \Carbon\Carbon::parse($client->last_contact)->diffForHumans() : '-' }}
                            </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL: ADD CLIENT -->
    <dialog id="add-client-modal" class="rounded-3xl p-0 backdrop:bg-neutral-900/50 backdrop:backdrop-blur-sm w-full max-w-lg bg-transparent shadow-2xl">
        <div class="bg-white p-8 relative overflow-hidden" x-data="{ type: 'particulier' }">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-serif font-bold text-neutral-900">Nouveau Dossier</h3>
                <form method="dialog"><button class="text-neutral-400 hover:text-neutral-900">✕</button></form>
            </div>
            
            <form action="{{ route('tools.sales.store') }}" method="POST" class="space-y-5">
                @csrf
                
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

                <!-- Particulier Fields -->
                <div class="space-y-4">
                    <input type="text" name="name" required placeholder="Nom & Prénom" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] placeholder-neutral-400 font-medium">
                    
                    <div x-show="type === 'professionnel'" x-transition class="space-y-4">
                         <input type="text" name="company_name" placeholder="Nom de la Société / Cabinet" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] placeholder-neutral-400">
                         
                         <!-- Custom Animated Dropdown -->
                         <div class="relative" x-data="{ open: false, selected: '', selectedLabel: 'Catégorie Professionnelle' }">
                            <input type="hidden" name="professional_category" x-model="selected">
                            
                            <button type="button" @click="open = !open" @click.away="open = false" 
                                class="w-full bg-neutral-50 rounded-xl px-4 py-3 text-sm flex justify-between items-center transition-all duration-200 focus:ring-2 focus:ring-[#E6AF5D]"
                                :class="open ? 'ring-2 ring-[#E6AF5D] shadow-sm' : ''">
                                <span x-text="selectedLabel" :class="selected ? 'text-neutral-900 font-medium' : 'text-neutral-400'"></span>
                                <svg class="w-4 h-4 text-neutral-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-xl border border-neutral-100 z-50 overflow-hidden">
                                
                                <div class="p-1 space-y-1">
                                    <template x-for="(label, value) in { 'revendeur': 'Revendeur', 'architecte': 'Architecte', 'promoteur': 'Promoteur' }">
                                        <button type="button" 
                                            @click="selected = value; selectedLabel = label; open = false"
                                            class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm transition-colors hover:bg-neutral-50 group"
                                            :class="selected === value ? 'bg-[#E6AF5D]/10 text-[#E6AF5D] font-bold' : 'text-neutral-600'">
                                            
                                            <!-- Icon based on type (simplified logic) -->
                                            <span class="w-6 h-6 rounded bg-neutral-100 flex items-center justify-center mr-3 text-xs transition-colors group-hover:bg-white group-hover:shadow-sm" 
                                                  :class="selected === value ? 'bg-[#E6AF5D] text-white' : 'text-neutral-400'">
                                                <span x-text="label.charAt(0)"></span>
                                            </span>
                                            
                                            <span x-text="label"></span>
                                            
                                            <svg x-show="selected === value" class="w-4 h-4 ml-auto text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </div>
                         </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="phone" required placeholder="Téléphone (Requis)" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] placeholder-neutral-400">
                        <input type="email" name="email" placeholder="Email" class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] placeholder-neutral-400">
                    </div>

                    <div>
                        <input type="text" name="interest_tags" placeholder="Intérêts: Marbre, Grès, Sanitaire..." class="w-full bg-neutral-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#E6AF5D] placeholder-neutral-400">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-neutral-900 text-white font-bold py-4 rounded-xl hover:bg-black transition-colors shadow-lg shadow-neutral-200">
                        Créer le Dossier
                    </button>
                    <p class="text-center text-[10px] text-neutral-400 mt-3">Ce client sera ajouté au CRM principal.</p>
                </div>
            </form>
        </div>
    </dialog>

</div>
@endsection