@extends('layouts.app')

@section('title', 'Agenda')
@section('page-title', 'Agenda & Suivi')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- LEFT: TASKS LIST -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="font-bold text-lg text-gray-900">Priorités de Relance</h3>
                    <p class="text-xs text-gray-500 mt-1">Dossiers classés par ancienneté (à traiter en premier).</p>
                </div>
                <span class="bg-black text-white text-xs font-bold px-3 py-1 rounded-full">{{ $upcoming->count() }} dossiers</span>
            </div>
            
            <div class="divide-y divide-gray-100">
                @forelse($upcoming as $client)
                    <div class="group p-5 hover:bg-gray-50 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Initials Avatar -->
                            <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center font-bold text-gray-400 group-hover:border-[#E6AF5D] group-hover:text-[#E6AF5D] transition-colors shadow-sm">
                                {{ strtoupper(substr($client->full_name, 0, 2)) }}
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-900 group-hover:text-[#E6AF5D] transition-colors">{{ $client->full_name }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ $client->updated_at->diffForHumans() }}
                                    </div>
                                    @if($client->phone)
                                        <span class="text-xs text-gray-400 border-l border-gray-200 pl-3">{{ $client->phone }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <!-- Status Badge -->
                            <span class="px-3 py-1 rounded-md text-xs font-bold border {{ $client->status == 'follow_up' ? 'bg-[#FFFBEB] text-[#B45309] border-[#E6AF5D]/30' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                {{ $client->status == 'follow_up' ? 'À Relancer' : 'Visité' }}
                            </span>

                            <!-- Action Button -->
                            <a href="{{ route('clients.show', $client->id) }}" class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 hover:bg-black hover:text-white hover:border-black transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Tout est à jour !</h3>
                        <p class="text-gray-500 mt-2 text-sm">Aucun client en attente de relance.</p>
                        <a href="{{ route('clients.create') }}" class="inline-block mt-4 text-[#E6AF5D] font-bold text-sm hover:underline">Ajouter un nouveau prospect →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- RIGHT: PERFORMANCE WIDGET -->
    <div class="space-y-6">
        
        <!-- Weekly Performance Card -->
        <div class="bg-black text-white p-6 rounded-2xl shadow-xl relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>

            <h3 class="font-bold text-lg mb-1 relative z-10">Performance Semaine</h3>
            <p class="text-gray-400 text-xs mb-8 relative z-10">Basé sur votre activité récente.</p>
            
            <!-- Metric 1: Quotes -->
            <div class="mb-6 relative z-10">
                <div class="flex items-end justify-between mb-2">
                    <span class="text-sm font-medium text-gray-300">Devis générés</span>
                    <span class="font-bold text-[#E6AF5D]">{{ $quotesThisWeek }}</span>
                </div>
                <div class="w-full bg-gray-800 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-[#E6AF5D] h-1.5 rounded-full transition-all duration-1000" style="width: {{ $quoteProgress }}%"></div>
                </div>
                <p class="text-[10px] text-gray-500 mt-1">Objectif indicatif : 5 / semaine</p>
            </div>

            <!-- Metric 2: Pending -->
            <div class="relative z-10">
                <div class="flex items-end justify-between mb-2">
                    <span class="text-sm font-medium text-gray-300">Relances en attente</span>
                    <span class="font-bold text-white">{{ $pendingFollowUps }}</span>
                </div>
                <div class="w-full bg-gray-800 h-1.5 rounded-full">
                    <div class="bg-white h-1.5 rounded-full" style="width: {{ min(100, $pendingFollowUps * 5) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Quick Tip Card -->
        <div class="bg-[#FFFBEB] p-6 rounded-2xl border border-[#E6AF5D]/30">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#E6AF5D] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h4 class="font-bold text-amber-900 text-sm mb-1">Conseil Pro</h4>
                    <p class="text-xs text-amber-800/80 leading-relaxed">
                        Les clients contactés dans les 48h après une visite showroom ont 60% plus de chances de signer un devis. Priorisez vos "Visités" récents.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection