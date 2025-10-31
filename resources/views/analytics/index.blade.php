@extends('layouts.app')

@section('title', 'Analytiques')
@section('page-title', 'Analytiques')
@section('page-description', 'Comprendre vos clients et améliorer vos performances commerciales')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Clients -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500 mb-1">Total clients</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $totalClients }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- New Clients (30 days) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500 mb-1">Nouveaux (30j)</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $recentClients }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-xl">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Clients with Quotes -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-500 mb-1">Devis demandés</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $clientsWithQuotes }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <!--<div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:shadow-md transition-shadow">-->
        <!--    <div class="flex items-center justify-between">-->
        <!--                       <div>-->
        <!--            <p class="text-sm font-medium text-orange-100 mb-1">Taux de conversion</p>-->
        <!--            <p class="text-4xl font-bold">{{ $conversionRate }}%</p>-->
        <!--        </div>-->
        <!--        <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">-->
        <!--            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
        <!--                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>-->
        <!--            </svg>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
    </div>

    <!-- Main Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sources Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200">
            <div class="px-6 py-5 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">D'où viennent vos clients ?</h3>
                <p class="text-sm text-neutral-500 mt-1">Sources d'acquisition les plus performantes</p>
            </div>
            <div class="p-6">
                @if(count($sources) > 0)
                    <div class="space-y-4">
                        @foreach($sources as $source => $data)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-neutral-700">{{ $source }}</span>
                                    <span class="text-sm font-semibold text-neutral-900">{{ $data['count'] }} clients ({{ $data['percentage'] }}%)</span>
                                </div>
                                <div class="w-full bg-neutral-100 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-neutral-100 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">Aucune donnée disponible</h3>
                        <p class="text-sm text-neutral-500">Ajoutez des clients pour voir les sources d'acquisition</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Products Interest -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200">
            <div class="px-6 py-5 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">Produits les plus demandés</h3>
                <p class="text-sm text-neutral-500 mt-1">Ce qui intéresse vos clients</p>
            </div>
            <div class="p-6">
                @if(count($translatedProducts) > 0)
                    <div class="space-y-4">
                        @foreach($translatedProducts as $product => $data)
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-neutral-700">{{ $product }}</span>
                                    <span class="text-sm font-semibold text-neutral-900">{{ $data['count'] }} clients ({{ $data['percentage'] }}%)</span>
                                </div>
                                <div class="w-full bg-neutral-100 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-neutral-100 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">Aucune donnée disponible</h3>
                        <p class="text-sm text-neutral-500">Les préférences produits apparaîtront ici</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Cities -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200">
            <div class="px-6 py-5 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">Villes principales</h3>
                <p class="text-sm text-neutral-500 mt-1">Distribution géographique de vos clients</p>
            </div>
            <div class="p-6">
                @if($cities->count() > 0)
                    <div class="space-y-3">
                        @foreach($cities as $city => $count)
                            <div class="flex justify-between items-center p-4 bg-neutral-50 rounded-xl hover:bg-neutral-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    <span class="font-medium text-neutral-900">{{ $city }}</span>
                                </div>
                                <span class="text-sm font-semibold text-neutral-600">{{ $count }} {{ $count === 1 ? 'client' : 'clients' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-neutral-100 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">Aucune donnée de ville</h3>
                        <p class="text-sm text-neutral-500">Ajoutez les villes pour voir la distribution géographique</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Client Types -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200">
            <div class="px-6 py-5 border-b border-neutral-200">
                <h3 class="text-lg font-semibold text-neutral-900">Types de clients</h3>
                <p class="text-sm text-neutral-500 mt-1">Répartition particuliers vs professionnels</p>
            </div>
            <div class="p-6">
                @if(isset($clientTypes) && count($clientTypes) > 0)
                    <div class="space-y-6">
                        @foreach($clientTypes as $type => $count)
                            @php
                                $percentage = $totalClients > 0 ? round(($count / $totalClients) * 100, 1) : 0;
                                $isParticulier = $type === 'particulier';
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center">
                                        <div class="p-2 {{ $isParticulier ? 'bg-blue-50' : 'bg-purple-50' }} rounded-lg mr-3">
                                            <svg class="w-6 h-6 {{ $isParticulier ? 'text-blue-600' : 'text-purple-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if($isParticulier)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                @endif
                                            </svg>
                                        </div>
                                        <span class="font-medium text-neutral-900">{{ $type === 'particulier' ? 'Particuliers' : 'Professionnels' }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-neutral-900">{{ $count }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-neutral-100 rounded-full h-3">
                                    <div class="bg-gradient-to-r {{ $isParticulier ? 'from-blue-500 to-blue-600' : 'from-purple-500 to-purple-600' }} h-3 rounded-full transition-all duration-700 shadow-sm" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-neutral-100 rounded-2xl mb-4">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-neutral-900 mb-1">Aucune donnée disponible</h3>
                        <p class="text-sm text-neutral-500">Les types de clients apparaîtront ici</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Rep Performance (Admin Only) -->
@if(isset($repStats) && count($repStats) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 mt-8">
        <div class="px-6 py-5 border-b border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-900">Performance des commerciaux</h3>
            <p class="text-sm text-neutral-500 mt-1">Vue d'ensemble de l'activité de chaque commercial</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($repStats as $repData)
                    <div class="border border-neutral-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-neutral-900">{{ $repData['rep']->name }}</h4>
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-full">Commercial</span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Total clients</span>
                                <span class="font-semibold text-neutral-900">{{ $repData['total_clients'] }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-neutral-600">Nouveaux (30j)</span>
                                <span class="font-semibold text-blue-600">{{ $repData['recent_clients'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

    <style>
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .bg-white {
            animation: slideIn 0.5s ease forwards;
        }
    </style>
@endsection