@extends('layouts.app')

@section('title', 'Performance - Cockpit Commercial')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- BACK NAV -->
    <a href="{{ route('tools.sales.index') }}" class="inline-flex items-center text-sm font-bold text-neutral-400 hover:text-neutral-900 mb-8 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au Tableau de Bord
    </a>

    <!-- KPI ROW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-neutral-900 rounded-3xl p-8 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#E6AF5D] rounded-full blur-3xl opacity-20 -mr-10 -mt-10 group-hover:opacity-30 transition-opacity"></div>
            <h3 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-2">Taux Conversion</h3>
            <div class="text-5xl font-serif font-bold text-[#E6AF5D] mb-2">{{ $conversionRate }}%</div>
            <p class="text-sm text-neutral-400">Clients Actifs ({{ $active }}/{{ $total }})</p>
        </div>
        
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-4">Objectif Mensuel</h3>
            <div class="flex items-end gap-3 mb-2">
                <span class="text-4xl font-bold text-neutral-900">85%</span>
                <span class="text-sm text-green-500 font-bold mb-1">▲ +5%</span>
            </div>
            <div class="w-full bg-neutral-100 rounded-full h-2 mt-4">
                <div class="bg-neutral-900 h-2 rounded-full" style="width: 85%"></div>
            </div>
        </div>
        
        <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-sm flex flex-col justify-center items-center text-center">
             <div class="w-16 h-16 rounded-full bg-[#E6AF5D]/10 flex items-center justify-center text-[#E6AF5D] mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <h3 class="text-lg font-bold text-neutral-900">Top Performer</h3>
            <p class="text-sm text-neutral-500">Vous êtes dans le top 10% des commerciaux cette semaine.</p>
        </div>
    </div>

    <!-- MAIN CHART -->
    <div class="bg-white rounded-3xl p-8 border border-neutral-100 shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <h3 class="font-bold text-lg text-neutral-900">Évolution du Chiffre d'Affaires</h3>
            <select class="bg-neutral-50 border-none text-sm font-bold rounded-lg px-4 py-2 text-neutral-600">
                <option>6 Derniers Mois</option>
                <option>Cette Année</option>
            </select>
        </div>
        
        <div class="h-80 w-full">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($monthlyPerformance['labels']),
            datasets: [{
                label: 'Ventes (BLs)',
                data: @json($monthlyPerformance['data']),
                borderColor: '#E6AF5D',
                backgroundColor: 'rgba(230, 175, 93, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#000',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { display: false }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
