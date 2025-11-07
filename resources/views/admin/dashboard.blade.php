@extends('layouts.app')

@section('title', 'Tableau de bord Admin')
@section('page-title', 'Tableau de bord Admin')
@section('page-description', 'Vue d\'ensemble de tous les clients, commerciaux et performances de l\'entreprise')

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Clients</p>
                    <h3 class="text-3xl font-bold">{{ $totalClients }}</h3>
                    <p class="text-blue-100 text-xs mt-2">
                        <span class="font-semibold">+{{ $recentClients->count() }}</span> cette semaine
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Commerciaux Actifs</p>
                    <h3 class="text-3xl font-bold">{{ $totalReps }}</h3>
                    <p class="text-green-100 text-xs mt-2">
                        <span class="font-semibold">{{ number_format($totalClients / max($totalReps, 1), 1) }}</span> clients/commercial
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Taux de Conversion</p>
                    <h3 class="text-3xl font-bold">{{ $conversionRate ?? 0 }}%</h3>
                    <p class="text-orange-100 text-xs mt-2">
                        <span class="font-semibold">{{ $customersCount ?? 0 }}</span> clients actifs
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $visitedCount ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">A visité</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $purchasedCount ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Ont acheté</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 text-center">
            <div class="text-2xl font-bold text-orange-600">{{ $followUpCount ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">À recontacter</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $prospectsCount ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Prospects</div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 text-center">
            <div class="text-2xl font-bold text-gray-600">{{ $inactiveCount ?? 0 }}</div>
            <div class="text-xs text-gray-500 mt-1">Inactifs</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Recent Clients -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Clients récents</h3>
                        <p class="text-sm text-gray-500 mt-1">Derniers ajouts à la base de données</p>
                    </div>
                    <a href="{{ route('admin.clients') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Voir tout
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="p-6">
                @if($recentClients->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="text-left text-xs uppercase text-gray-500 border-b border-gray-200">
                                    <th class="pb-3 font-semibold">Client</th>
                                    <th class="pb-3 font-semibold">Commercial</th>
                                    <th class="pb-3 font-semibold">Type</th>
                                    <th class="pb-3 font-semibold">Statut</th>
                                    <th class="pb-3 font-semibold">Ajouté</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @foreach($recentClients as $client)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="py-4">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $client->display_name }}</div>
                                                <div class="text-xs text-gray-500 flex items-center mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $client->city ?? 'Non renseigné' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                                                    <span class="text-xs font-semibold text-gray-600">{{ strtoupper(substr($client->user->name, 0, 2)) }}</span>
                                                </div>
                                                <span class="text-gray-700 font-medium">{{ $client->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $client->client_type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $client->client_type === 'company' ? 'Entreprise' : 'Particulier' }}
                                            </span>
                                        </td>
                                        <td class="py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $client->status === 'purchased' ? 'bg-green-100 text-green-800' : 
                                                   ($client->status === 'visited' ? 'bg-blue-100 text-blue-800' : 
                                                   ($client->status === 'follow_up' ? 'bg-orange-100 text-orange-800' : 
                                                   ($client->status === 'prospect' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                                @if($client->status === 'visited')
                                                    A visité
                                                @elseif($client->status === 'purchased')
                                                    Client
                                                @elseif($client->status === 'follow_up')
                                                    À recontacter
                                                @elseif($client->status === 'prospect')
                                                    Prospect
                                                @elseif($client->status === 'inactive')
                                                    Inactif
                                                @else
                                                    {{ ucfirst($client->status) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="py-4 text-gray-500">
                                            <div class="text-sm">{{ $client->created_at->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ $client->created_at->diffForHumans() }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
                        <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier client.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Top Performers -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Meilleurs commerciaux</h3>
                    <p class="text-sm text-gray-500 mt-1">Performance du mois</p>
                </div>
                <div class="p-6">
                    @if(isset($topReps) && count($topReps) > 0)
                        <div class="space-y-4">
                            @foreach($topReps as $index => $rep)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                            {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : 
                                               ($index === 1 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') }}">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="font-semibold text-gray-900">{{ $rep->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $rep->clients_count }} clients</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-semibold text-green-600">
                                            {{ $rep->customers_count ?? 0 }} ventes
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 text-sm">
                            Aucune donnée disponible
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.clients') }}" class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                                <svg class="w-5 h-5 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700">Tous les clients</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    
                    <a href="{{ route('admin.analytics') }}" class="flex items-center justify-between p-3 bg-white rounded-lg hover:shadow-md transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                                <svg class="w-5 h-5 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <span class="ml-3 font-medium text-gray-700">Voir analytiques</span>
                        </div>
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection