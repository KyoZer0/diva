@extends('layouts.app')

@section('title', 'Commerciaux')
@section('page-title', 'Équipe Commerciale')

@section('content')

    <!-- TOP CONTROL BAR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900 tracking-tight">Performance Équipe</h1>
            <p class="text-neutral-500 text-sm mt-1">Classement et analyse individuelle des commerciaux.</p>
        </div>
        
        <!-- Global Stats Pills -->
        <div class="flex gap-3">
            <div class="bg-white px-4 py-2 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-3">
                <div class="p-1.5 bg-neutral-900 rounded-lg text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase">Effectif</span>
                    <span class="block text-base font-bold text-neutral-900">{{ $repStats->count() }}</span>
                </div>
            </div>

            <div class="bg-white px-4 py-2 rounded-xl border border-neutral-200 shadow-sm flex items-center gap-3">
                <div class="p-1.5 bg-[#E6AF5D] rounded-lg text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <span class="block text-xs font-bold text-neutral-400 uppercase">Ventes Totales</span>
                    <span class="block text-base font-bold text-neutral-900">{{ $repStats->sum('purchased_clients') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. PODIUM (TOP 3) -->
    @if($repStats->count() >= 3)
    @php
        // Advanced Sorting: Sales > Quotes > Total Clients
        $sortedReps = $repStats->sort(function ($a, $b) {
            // 1. Primary: Ventes (Purchased)
            if ($a['purchased_clients'] !== $b['purchased_clients']) {
                return $b['purchased_clients'] <=> $a['purchased_clients'];
            }
            // 2. Secondary: Devis (Quotes)
            if ($a['clients_with_quotes'] !== $b['clients_with_quotes']) {
                return $b['clients_with_quotes'] <=> $a['clients_with_quotes'];
            }
            // 3. Tertiary: Total Volume
            return $b['total_clients'] <=> $a['total_clients'];
        })->values();

        $gold = $sortedReps[0];
        $silver = $sortedReps[1];
        $bronze = $sortedReps[2];
    @endphp

    <div class="flex flex-col md:flex-row items-end justify-center gap-4 md:gap-6 mb-12 px-4">
        
        <!-- #2 SILVER (Left) -->
        <div class="w-full md:w-1/3 order-2 md:order-1 flex flex-col">
            <div class="bg-white rounded-t-2xl rounded-b-xl border border-neutral-200 shadow-sm relative p-6 text-center group hover:border-neutral-300 transition-all">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-neutral-200 text-neutral-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm ring-4 ring-white">
                    #2 Argent
                </div>
                
                <div class="mt-4 mb-3">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 mx-auto flex items-center justify-center text-xl font-bold text-neutral-500 border-2 border-neutral-200">
                        {{ strtoupper(substr($silver['rep']->name, 0, 2)) }}
                    </div>
                </div>
                
                <h3 class="font-bold text-neutral-900 truncate">{{ $silver['rep']->name }}</h3>
                <div class="text-xs text-neutral-500 font-medium mb-4">{{ $silver['purchased_clients'] }} Ventes</div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-2 border-t border-neutral-100 pt-3">
                    <div class="text-center">
                        <span class="block text-sm font-bold text-neutral-900">{{ $silver['clients_with_quotes'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase">Devis</span>
                    </div>
                    <div class="text-center border-l border-neutral-100">
                        <span class="block text-sm font-bold text-neutral-900">{{ $silver['total_clients'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase">Total</span>
                    </div>
                </div>
            </div>
            <!-- Pedestal Visual -->
            <div class="h-4 bg-neutral-200 rounded-b-2xl mx-2 opacity-50"></div>
        </div>

        <!-- #1 GOLD (Center - Taller) -->
        <div class="w-full md:w-1/3 order-1 md:order-2 flex flex-col -mt-8 md:-mt-0">
            <!-- Crown Icon -->
            <div class="text-center mb-2 animate-bounce">
                <svg class="w-8 h-8 mx-auto text-[#E6AF5D]" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            </div>
            
            <div class="bg-white rounded-t-2xl rounded-b-xl border-2 border-[#E6AF5D] shadow-xl relative p-8 text-center z-10 transform scale-105">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-[#E6AF5D] text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-md ring-4 ring-white">
                    #1 Champion
                </div>

                <div class="mt-4 mb-4">
                    <div class="w-20 h-20 rounded-full bg-neutral-900 mx-auto flex items-center justify-center text-2xl font-bold text-[#E6AF5D] border-4 border-[#E6AF5D]/20 shadow-lg">
                        {{ strtoupper(substr($gold['rep']->name, 0, 2)) }}
                    </div>
                </div>
                
                <h3 class="text-lg font-bold text-neutral-900 truncate">{{ $gold['rep']->name }}</h3>
                <div class="text-sm font-bold text-[#E6AF5D] mb-6">{{ $gold['purchased_clients'] }} Ventes Réussies</div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-2 border-t border-neutral-100 pt-4">
                    <div class="text-center">
                        <span class="block text-lg font-bold text-neutral-900">{{ $gold['clients_with_quotes'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase font-bold">Devis Signés</span>
                    </div>
                    <div class="text-center border-l border-neutral-100">
                        <span class="block text-lg font-bold text-neutral-900">{{ $gold['total_clients'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase font-bold">Total Clients</span>
                    </div>
                </div>
            </div>
            <!-- Pedestal Visual -->
            <div class="h-8 bg-[#E6AF5D]/20 rounded-b-2xl mx-2"></div>
        </div>

        <!-- #3 BRONZE (Right) -->
        <div class="w-full md:w-1/3 order-3 md:order-3 flex flex-col">
            <div class="bg-white rounded-t-2xl rounded-b-xl border border-neutral-200 shadow-sm relative p-6 text-center group hover:border-amber-700/30 transition-all">
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-amber-700 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm ring-4 ring-white">
                    #3 Bronze
                </div>
                
                <div class="mt-4 mb-3">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 mx-auto flex items-center justify-center text-xl font-bold text-neutral-500 border-2 border-amber-700/20">
                        {{ strtoupper(substr($bronze['rep']->name, 0, 2)) }}
                    </div>
                </div>
                
                <h3 class="font-bold text-neutral-900 truncate">{{ $bronze['rep']->name }}</h3>
                <div class="text-xs text-neutral-500 font-medium mb-4">{{ $bronze['purchased_clients'] }} Ventes</div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-2 border-t border-neutral-100 pt-3">
                    <div class="text-center">
                        <span class="block text-sm font-bold text-neutral-900">{{ $bronze['clients_with_quotes'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase">Devis</span>
                    </div>
                    <div class="text-center border-l border-neutral-100">
                        <span class="block text-sm font-bold text-neutral-900">{{ $bronze['total_clients'] }}</span>
                        <span class="text-[10px] text-neutral-400 uppercase">Total</span>
                    </div>
                </div>
            </div>
            <!-- Pedestal Visual -->
            <div class="h-3 bg-amber-700/20 rounded-b-2xl mx-2 opacity-50"></div>
        </div>
    </div>
    @endif

    <!-- 2. ALL REPS GRID -->
    <h3 class="text-lg font-bold text-neutral-900 mb-4">Tous les Commerciaux</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($repStats as $stat)
            @php
                // Calculation Fix: Avoid Division by Zero
                $total = $stat['total_clients'];
                $sold = $stat['purchased_clients'];
                $quoted = $stat['clients_with_quotes'];
                
                // Calculate percentages for the pipeline bar
                $pctSold = $total > 0 ? ($sold / $total) * 100 : 0;
                $pctQuote = $total > 0 ? ($quoted / $total) * 100 : 0;
                // Note: Quoted usually overlaps with Sold, so for visual bar, we adjust
                // Visual Logic: Sold (Black), Quote-Only (Gold), Visited-Only (Gray)
                $visSold = $pctSold;
                $visQuote = max(0, $pctQuote - $pctSold); // Quotes that aren't sold yet
                $visRest = 100 - ($visSold + $visQuote);

                $conversionRate = $total > 0 ? round(($sold / $total) * 100, 1) : 0;
                $initials = strtoupper(substr($stat['rep']->name, 0, 2));
            @endphp

            <div class="bg-white rounded-2xl border border-neutral-200 shadow-sm hover:border-[#E6AF5D] transition-all duration-300 flex flex-col relative overflow-hidden group">
                
                <!-- Card Header -->
                <div class="p-6 pb-4 flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-neutral-900 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                            {{ $initials }}
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-neutral-900 group-hover:text-[#E6AF5D] transition-colors">{{ $stat['rep']->name }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 bg-neutral-100 text-neutral-600 text-[10px] font-bold rounded uppercase">
                                    {{ $stat['recent_clients'] }} Nouveaux (30j)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <span class="block text-2xl font-bold text-neutral-900">{{ $conversionRate }}%</span>
                        <span class="text-[10px] font-bold text-neutral-400 uppercase">Taux Transfo.</span>
                    </div>
                </div>

                <!-- Pipeline Visualization (New Feature) -->
                <div class="px-6 mb-4">
                    <div class="flex justify-between text-[10px] font-bold text-neutral-400 mb-1 uppercase tracking-wide">
                        <span>Pipeline Client</span>
                        <span>{{ $total }} Total</span>
                    </div>
                    <div class="w-full h-2 bg-neutral-100 rounded-full overflow-hidden flex">
                        <!-- Sold -->
                        <div class="h-full bg-neutral-900" style="width: {{ $visSold }}%" title="Vendu: {{ $sold }}"></div>
                        <!-- Quote -->
                        <div class="h-full bg-[#E6AF5D]" style="width: {{ $visQuote }}%" title="Devis en cours: {{ $quoted - $sold }}"></div>
                        <!-- Empty/Visited -->
                    </div>
                    <div class="flex gap-4 mt-2 text-[10px] font-medium text-neutral-500">
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-neutral-900"></span> {{ $sold }} Ventes</div>
                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#E6AF5D]"></span> {{ $quoted }} Devis</div>
                    </div>
                </div>

                <div class="border-t border-neutral-100"></div>

                <!-- Footer / Actions -->
                <div class="p-4 flex gap-3 bg-neutral-50/50">
                    <a href="{{ route('admin.rep-details', $stat['rep']->id) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-white border border-neutral-200 text-neutral-700 text-sm font-bold rounded-lg hover:border-neutral-300 hover:shadow-sm transition-all">
                        Voir les statistiques
                    </a>
                    <a href="{{ route('admin.rep-export', $stat['rep']->id) }}" class="inline-flex items-center justify-center w-10 h-10 bg-neutral-900 text-white rounded-lg hover:bg-black transition-all" title="Exporter Excel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </div>

            </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($repStats->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 p-16 text-center mt-6">
            <div class="w-16 h-16 bg-neutral-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-neutral-900">Aucun commercial</h3>
            <p class="text-neutral-500 mt-2">Commencez par ajouter des membres à votre équipe.</p>
        </div>
    @endif

@endsection