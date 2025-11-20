@extends('layouts.app')

@section('title', 'Commerciaux')
@section('page-title', 'Performance Commerciale')

@section('content')

<div class="min-h-screen bg-slate-50 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:20px_20px] py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- 1. HEADER & GLOBAL KPI -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-10">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Équipe Commerciale</h1>
                <p class="text-slate-500 mt-2">Vue d'ensemble des performances et statistiques individuelles.</p>
            </div>

            <!-- Global Stats Strip -->
            <div class="flex gap-4 overflow-x-auto pb-2 lg:pb-0">
                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200/60 whitespace-nowrap">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Commerciaux</p>
                        <p class="text-lg font-bold text-slate-900">{{ $repStats->count() }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200/60 whitespace-nowrap">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Clients Actifs</p>
                        <p class="text-lg font-bold text-slate-900">{{ $repStats->sum('purchased_clients') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. REPS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($repStats as $stat)
                @php
                    $conversionRate = $stat['total_clients'] > 0 ? round(($stat['purchased_clients'] / $stat['total_clients']) * 100, 1) : 0;
                    $initials = strtoupper(substr($stat['rep']->name, 0, 2));
                @endphp

                <div class="group bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:border-indigo-100 transition-all duration-300 flex flex-col overflow-hidden">
                    
                    <!-- Card Header -->
                    <div class="p-6 pb-0 flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Avatar -->
                            <div class="relative">
                                <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-lg font-bold shadow-lg shadow-slate-200 group-hover:scale-105 transition-transform duration-300">
                                    {{ $initials }}
                                </div>
                                <!-- Status Dot -->
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-white rounded-full flex items-center justify-center">
                                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full ring-2 ring-white"></div>
                                </div>
                            </div>
                            
                            <!-- Name & Role -->
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 leading-tight group-hover:text-indigo-600 transition-colors">{{ $stat['rep']->name }}</h3>
                                <p class="text-sm text-slate-500 font-medium">{{ $stat['rep']->email }}</p>
                            </div>
                        </div>

                        <!-- Menu/Option Icon -->
                        <a href="{{ route('admin.rep-details', $stat['rep']->id) }}" class="text-slate-300 hover:text-slate-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                        </a>
                    </div>

                    <!-- Data Strip (Divided Grid) -->
                    <div class="mt-6 px-6">
                        <div class="grid grid-cols-3 border-y border-slate-100 divide-x divide-slate-100 py-4">
                            <div class="text-center px-2">
                                <span class="block text-xl font-bold text-slate-900">{{ $stat['total_clients'] }}</span>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Total</span>
                            </div>
                            <div class="text-center px-2">
                                <span class="block text-xl font-bold text-indigo-600">{{ $stat['recent_clients'] }}</span>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Nouveaux</span>
                            </div>
                            <div class="text-center px-2">
                                <span class="block text-xl font-bold text-amber-600">{{ $stat['clients_with_quotes'] }}</span>
                                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Devis</span>
                            </div>
                        </div>
                    </div>

                    <!-- Performance / Footer -->
                    <div class="p-6 pt-4 flex-1 flex flex-col justify-end">
                        
                        <!-- Conversion Bar -->
                        <div class="mb-6">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-xs font-semibold text-slate-600">Taux de conversion</span>
                                <span class="text-sm font-bold {{ $conversionRate >= 50 ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $conversionRate }}%
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $conversionRate >= 50 ? 'bg-emerald-500' : 'bg-slate-900' }}" 
                                     style="width: {{ $conversionRate }}%"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <a href="{{ route('admin.rep-details', $stat['rep']->id) }}" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-xl hover:bg-slate-800 transition-all shadow-sm hover:shadow-md">
                                Voir le profil
                            </a>
                            <a href="{{ route('admin.rep-export', $stat['rep']->id) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all" title="Exporter">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        @if($repStats->isEmpty())
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-16 text-center mt-6">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Aucun commercial</h3>
                <p class="text-slate-500 mt-2">Commencez par ajouter des membres à votre équipe commerciale.</p>
            </div>
        @endif
    </div>
</div>
@endsection