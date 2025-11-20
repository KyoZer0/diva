@extends('layouts.app')

@section('title', 'Détails Commercial')
@section('page-title', $rep->name)
@section('page-description', 'Performance et analytiques du commercial')

@section('header-actions')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.rep-export', $rep->id) }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-black text-black rounded-lg text-sm font-medium hover:bg-black hover:text-white transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Exporter CSV
        </a>
        <a href="{{ route('admin.reps') }}" class="inline-flex items-center px-4 py-2 bg-white border-2 border-black text-black rounded-lg text-sm font-medium hover:bg-black hover:text-white transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour aux commerciaux
        </a>
    </div>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Clients -->
        <div class="bg-white rounded-xl border-2 border-black p-6">
            <div class="text-sm font-medium text-gray-600 mb-2">Total clients</div>
            <div class="text-4xl font-bold text-black">{{ $totalClients }}</div>
        </div>

        <!-- New Clients (30 days) -->
        <div class="bg-amber-50 rounded-xl border-2 border-amber-300 p-6">
            <div class="text-sm font-medium text-amber-700 mb-2">Nouveaux (30j)</div>
            <div class="text-4xl font-bold text-amber-900">{{ $recentClients }}</div>
        </div>

        <!-- Clients with Quotes -->
        <div class="bg-white rounded-xl border-2 border-gray-300 p-6">
            <div class="text-sm font-medium text-gray-600 mb-2">Devis demandés</div>
            <div class="text-4xl font-bold text-gray-900">{{ $clientsWithQuotes }}</div>
        </div>
    </div>

    <!-- Sources Chart -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-black">Sources d'acquisition</h3>
        </div>
        <div class="p-6">
            @if(count($sources) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($sources as $source => $data)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-gray-600 mb-2">{{ $source }}</div>
                            <div class="text-2xl font-bold text-black mb-3">{{ $data['count'] }}</div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full transition-all duration-500" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">{{ $data['percentage'] }}% du total</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-sm text-gray-500">Aucune donnée disponible</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Status Distribution -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-bold text-black">Statuts clients</h3>
            </div>
            <div class="p-6">
                @if(isset($statusDistribution) && count($statusDistribution) > 0)
                    <div class="space-y-3">
                        @foreach($statusDistribution as $status => $count)
                            @php
                                $statusLabels = [
                                    'visited' => 'A visité',
                                    'purchased' => 'Client',
                                    'follow_up' => 'À recontacter',
                                    'prospect' => 'Prospect',
                                    'inactive' => 'Inactif'
                                ];
                                $label = $statusLabels[$status] ?? ucfirst($status);
                                $percentage = $totalClients > 0 ? round(($count / $totalClients) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                    <span class="text-sm font-bold text-black">{{ $count }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-{{ $status === 'purchased' ? 'amber-500' : 'black' }} h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Cities -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-bold text-black">Villes principales</h3>
            </div>
            <div class="p-6">
                @if($cities->count() > 0)
                    <div class="space-y-2">
                        @foreach($cities as $city => $count)
                            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                <span class="font-medium text-gray-900">{{ $city }}</span>
                                <span class="text-sm font-bold text-black">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Client Types -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-bold text-black">Types de clients</h3>
            </div>
            <div class="p-6">
                @if(isset($clientTypes) && count($clientTypes) > 0)
                    <div class="space-y-5">
                        @foreach($clientTypes as $type => $count)
                            @php
                                $percentage = $totalClients > 0 ? round(($count / $totalClients) * 100, 1) : 0;
                                $isParticulier = $type === 'particulier';
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">{{ $type === 'particulier' ? 'Particuliers' : 'Professionnels' }}</span>
                                    <span class="text-sm font-bold text-black">{{ $count }} ({{ $percentage }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2.5">
                                    <div class="bg-{{ $isParticulier ? 'black' : 'amber-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <p class="text-sm text-gray-500">Aucune donnée disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Clients List -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-black">Clients récents</h3>
            <p class="text-sm text-gray-600 mt-1">Les 10 derniers clients ajoutés</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentClientsList as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-black rounded-full flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($client->full_name, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $client->full_name }}</div>
                                        @if($client->company_name)
                                            <div class="text-xs text-gray-500">{{ $client->company_name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $client->client_type === 'particulier' ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $client->client_type === 'particulier' ? 'Particulier' : 'Professionnel' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $client->city ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $client->status === 'purchased' ? 'bg-amber-100 text-amber-800' :
                                       ($client->status === 'follow_up' ? 'bg-gray-100 text-gray-800' :
                                       ($client->status === 'visited' ? 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-800')) }}">
                                    @if($client->status === 'visited')
                                        A visité
                                    @elseif($client->status === 'purchased')
                                        Client
                                    @elseif($client->status === 'follow_up')
                                        À recontacter
                                    @else
                                        {{ ucfirst($client->status) }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $client->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-900 rounded-lg hover:bg-amber-100 transition-colors">
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="text-sm text-gray-500">Aucun client trouvé</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

