@extends('layouts.app')

@section('title', 'Analytiques Avancées')
@section('page-title', 'Tableau de Bord')
@section('page-description', 'Analysez vos performances en temps réel')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- FILTER BAR -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-neutral-200 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <h2 class="font-bold text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
            Vue d'ensemble
        </h2>
        <div class="flex items-center gap-3">
            <label for="timeRange" class="text-sm font-medium text-neutral-500">Période</label>
            <select id="timeRange" class="bg-neutral-50 border border-neutral-200 text-neutral-900 text-sm rounded-lg focus:ring-[#E6AF5D] focus:border-[#E6AF5D] block w-full p-2.5">
                <option value="7">7 derniers jours</option>
                <option value="30" selected>30 derniers jours</option>
                <option value="90">Ce trimestre</option>
                <option value="365">Cette année</option>
            </select>
        </div>
    </div>

    <!-- 1. KPI CARDS (Global Stats) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Clients -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:border-black/30 transition-all">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Base Clients</p>
            <h3 class="text-3xl font-bold text-neutral-900 mt-2">{{ $totalClients }}</h3>
            <p class="text-sm text-neutral-500 mt-1 flex items-center">
                <span class="{{ $growthPercentage >= 0 ? 'text-emerald-500' : 'text-red-500' }} font-bold mr-1">
                    {{ $growthPercentage >= 0 ? '+' : '' }}{{ round($growthPercentage) }}%
                </span>
                vs mois dernier
            </p>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:border-[#E6AF5D]/50 transition-all">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Taux Conversion</p>
            <h3 class="text-3xl font-bold text-neutral-900 mt-2">{{ $conversionRate }}%</h3>
            <div class="w-full bg-neutral-100 rounded-full h-1.5 mt-3">
                <div class="bg-[#E6AF5D] h-1.5 rounded-full" style="width: {{ $conversionRate }}%"></div>
            </div>
        </div>

        <!-- Quotes (Pipeline) -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 hover:border-black/30 transition-all">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Devis en cours</p>
            <h3 class="text-3xl font-bold text-neutral-900 mt-2">{{ $clientsWithQuotes }}</h3>
            <p class="text-sm text-neutral-500 mt-1">Opportunités actives</p>
        </div>

        <!-- New Clients -->
        <div class="bg-black p-6 rounded-2xl shadow-sm border border-black text-white">
            <p class="text-xs font-bold text-neutral-400 uppercase tracking-widest">Nouveaux (Ce mois)</p>
            <h3 class="text-3xl font-bold text-white mt-2">{{ $thisMonth }}</h3>
            <p class="text-sm text-neutral-400 mt-1">Croissance active</p>
        </div>
    </div>

    <!-- 2. INTERACTIVE CHARTS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Large Line Chart: Trends -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-neutral-200">
            <h3 class="text-sm font-bold text-neutral-500 uppercase mb-6 flex justify-between">
                <span>Évolution des Leads</span>
                <span id="chartLabelRange" class="text-xs bg-neutral-100 px-2 py-1 rounded text-neutral-600">30 Jours</span>
            </h3>
            <div class="h-80 w-full relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Sources -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 flex flex-col">
            <h3 class="text-sm font-bold text-neutral-500 uppercase mb-6">Canaux d'acquisition</h3>
            <div class="flex-1 relative flex items-center justify-center">
                <canvas id="sourceChart" style="max-height: 250px;"></canvas>
            </div>
            <div class="mt-4 text-center">
                <a href="#sourceTable" class="text-xs text-[#E6AF5D] font-bold hover:underline">Voir détails ROI ↓</a>
            </div>
        </div>
    </div>

    <!-- 3. BAR CHART: PRODUCTS -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-neutral-200 mb-8">
        <h3 class="text-sm font-bold text-neutral-500 uppercase mb-6">Demande par Produit</h3>
        <div class="h-64 w-full relative">
            <canvas id="productChart"></canvas>
        </div>
    </div>

    <!-- 4. DETAILED DATA TABLES -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8" id="sourceTable">
        
        <!-- ROI Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
            <div class="p-6 border-b border-neutral-100">
                <h3 class="font-bold text-neutral-900">Performance Sources (Global)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-neutral-400 uppercase bg-neutral-50">
                        <tr>
                            <th class="px-6 py-3">Source</th>
                            <th class="px-6 py-3 text-center">Volume</th>
                            <th class="px-6 py-3 text-center">Conv. %</th>
                            <th class="px-6 py-3 text-right">Impact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($sourceIntelligence as $name => $data)
                        <tr class="hover:bg-neutral-50 transition">
                            <td class="px-6 py-3 font-medium">{{ $name }}</td>
                            <td class="px-6 py-3 text-center">{{ $data['count'] }}</td>
                            <td class="px-6 py-3 text-center font-bold {{ $data['conversion_rate'] > 20 ? 'text-emerald-600' : 'text-neutral-600' }}">
                                {{ $data['conversion_rate'] }}%
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="w-16 ml-auto bg-neutral-100 h-1 rounded-full">
                                    <div class="bg-neutral-900 h-1 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Rep Leaderboard -->
        <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
            <div class="p-6 border-b border-neutral-100">
                <h3 class="font-bold text-neutral-900">Top Commerciaux</h3>
            </div>
            <div class="p-4 space-y-3">
                @foreach($repStats->take(5) as $rep)
                <div class="flex items-center p-3 border border-neutral-100 rounded-xl hover:border-[#E6AF5D] transition group">
                    <div class="w-10 h-10 rounded-full bg-neutral-900 text-[#E6AF5D] flex items-center justify-center font-bold text-sm">
                        {{ substr($rep->name, 0, 1) }}
                    </div>
                    <div class="ml-3 flex-1">
                        <h4 class="font-bold text-sm text-neutral-900">{{ $rep->name }}</h4>
                        <p class="text-xs text-neutral-500">{{ $rep->clients_count }} Clients | {{ $rep->quotes_count }} Devis</p>
                    </div>
                    <div class="text-right">
                        <span class="block font-bold text-lg {{ $rep->conversion_rate > 25 ? 'text-emerald-600' : 'text-neutral-900' }}">
                            {{ $rep->conversion_rate }}%
                        </span>
                        <span class="text-[10px] text-neutral-400 uppercase">Conv.</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Initial Data passed from Controller
            let chartData = @json($chartData);
            
            // Canvas Contexts
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            const ctxSource = document.getElementById('sourceChart').getContext('2d');
            const ctxProduct = document.getElementById('productChart').getContext('2d');

            // Chart Instances
            let trendChart, sourceChart, productChart;

            // Function to Initialize/Update Charts
            function renderCharts(data) {
                
                // 1. TREND CHART (Line)
                if(trendChart) trendChart.destroy();
                trendChart = new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: data.trend.labels,
                        datasets: [{
                            label: 'Nouveaux Clients',
                            data: data.trend.data,
                            borderColor: '#E6AF5D',
                            backgroundColor: (context) => {
                                const ctx = context.chart.ctx;
                                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                                gradient.addColorStop(0, 'rgba(230, 175, 93, 0.4)');
                                gradient.addColorStop(1, 'rgba(230, 175, 93, 0)');
                                return gradient;
                            },
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#000',
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f3f4f6' } },
                            x: { grid: { display: false } }
                        },
                        interaction: { mode: 'nearest', axis: 'x', intersect: false }
                    }
                });

                // 2. SOURCE CHART (Doughnut)
                if(sourceChart) sourceChart.destroy();
                sourceChart = new Chart(ctxSource, {
                    type: 'doughnut',
                    data: {
                        labels: data.sources.labels,
                        datasets: [{
                            data: data.sources.data,
                            backgroundColor: ['#171717', '#E6AF5D', '#525252', '#d4d4d4', '#fbbf24'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                        },
                        cutout: '70%'
                    }
                });

                // 3. PRODUCT CHART (Bar)
                if(productChart) productChart.destroy();
                productChart = new Chart(ctxProduct, {
                    type: 'bar',
                    data: {
                        labels: data.products.labels,
                        datasets: [{
                            label: 'Demande',
                            data: data.products.data,
                            backgroundColor: '#171717',
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { display: false } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Initial Render
            renderCharts(chartData);

            // AJAX Filter Logic
            const selector = document.getElementById('timeRange');
            const labelRange = document.getElementById('chartLabelRange');

            selector.addEventListener('change', function() {
                const days = this.value;
                labelRange.textContent = days + " Jours";
                
                // Show loading state (opacity)
                document.getElementById('trendChart').style.opacity = '0.5';

                fetch(`{{ route('admin.analytics.data') }}?days=${days}`)
                    .then(response => response.json())
                    .then(newData => {
                        renderCharts(newData);
                        document.getElementById('trendChart').style.opacity = '1';
                    })
                    .catch(error => console.error('Error fetching data:', error));
            });
        });
    </script>

@endsection