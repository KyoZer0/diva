@extends('layouts.app')

@section('title', $name)
@section('content')

<div class="max-w-5xl mx-auto pb-20 pt-8">
    
    <!-- NAVIGATION -->
    <a href="{{ route('tools.logistics.articles.index') }}" class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-neutral-400 hover:text-black mb-8 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Retour au registre
    </a>

    <!-- PRODUCT HEADER -->
    <div class="bg-neutral-900 text-white p-12 rounded-3xl mb-12 relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-[#E6AF5D] rounded-full blur-[80px] opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <span class="font-mono text-xs text-[#E6AF5D] uppercase tracking-widest mb-2 block">Fiche Produit</span>
                    <h1 class="text-4xl md:text-5xl font-serif">{{ $name }}</h1>
                    @if($product->reference)
                    <p class="font-mono text-sm text-neutral-400 mt-2">REF: {{ $product->reference }}</p>
                    @endif
                </div>
                <button onclick="document.getElementById('editProductModal').classList.remove('hidden')" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-xs font-bold uppercase tracking-widest transition-colors backdrop-blur-sm">
                    ✎ Modifier Fiche
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 border-t border-white/10 pt-8">
                <!-- Global Stats -->
                <div>
                    <span class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-1">Total Sorti</span>
                    <span class="font-serif text-2xl text-white">{{ floatval($stats['total_volume']) }} <span class="text-sm text-neutral-400">{{ $stats['unit'] }}</span></span>
                </div>
                
                <!-- Packaging Info (New) -->
                <div>
                    <span class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-1">Conditionnement</span>
                    <div class="flex flex-col">
                        <span class="font-bold text-white text-sm">
                            {{ $product->default_boxes_per_pallet ? floatval($product->default_boxes_per_pallet) . ' crt/pal' : '--' }}
                        </span>
                        <span class="text-xs text-neutral-400">
                            {{ $product->default_conversion ? floatval($product->default_conversion) . ' m²/box' : '--' }}
                        </span>
                    </div>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-1">Dépôt Défaut</span>
                    <span class="font-bold text-white text-sm">{{ $product->default_warehouse ?? '--' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-neutral-500 uppercase tracking-widest mb-1">Dernier Mvt.</span>
                    <span class="font-mono text-sm text-[#E6AF5D]">{{ $stats['last_seen']->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editProductModal" class="hidden fixed inset-0 bg-neutral-900/90 z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg p-8 rounded-2xl relative shadow-2xl">
            <button onclick="document.getElementById('editProductModal').classList.add('hidden')" class="absolute top-4 right-4 text-neutral-400 hover:text-black">✕</button>
            
            <h3 class="font-serif text-2xl mb-6">Modifier Standard Produit</h3>
            
            <form action="{{ route('tools.logistics.articles.update_global') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="name" value="{{ $name }}">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">Boîtes / Palette</label>
                        <input type="number" step="1" name="boxes_per_pallet" value="{{ $product->default_boxes_per_pallet }}" class="w-full border-b border-neutral-300 py-2 font-bold focus:outline-none focus:border-[#E6AF5D]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">m² / Boîte</label>
                        <input type="number" step="0.001" name="conversion" value="{{ $product->default_conversion }}" class="w-full border-b border-neutral-300 py-2 font-bold focus:outline-none focus:border-[#E6AF5D]">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase text-neutral-400 mb-1">Dépôt par défaut</label>
                    <select name="warehouse" class="w-full border-b border-neutral-300 py-2 bg-transparent focus:outline-none focus:border-[#E6AF5D]">
                        <option value="">-- Aucun --</option>
                        @foreach(['Mediouna', 'S.M', 'Lkhyayta', 'Diva Ceramica'] as $wh)
                            <option value="{{ $wh }}" {{ $product->default_warehouse == $wh ? 'selected' : '' }}>{{ $wh }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full py-3 bg-black text-white rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-[#E6AF5D] transition-colors">
                    Sauvegarder
                </button>
            </form>
        </div>
    </div>

    <!-- MOVEMENT TIMELINE (Clean Ledger) -->
    <h3 class="font-serif text-xl text-neutral-900 italic mb-6">Historique des Chargements</h3>
    
    <div class="bg-white border border-neutral-200 shadow-sm rounded-xl overflow-hidden">
        @foreach($movements as $mvt)
        <div class="group flex flex-col md:flex-row items-center justify-between p-6 border-b border-neutral-100 hover:bg-neutral-50 transition-colors">
            
            <div class="flex items-center gap-6 w-full md:w-auto mb-4 md:mb-0">
                <!-- Date Box -->
                <div class="flex flex-col items-center justify-center w-12 h-12 bg-neutral-100 rounded-lg group-hover:bg-[#E6AF5D] group-hover:text-black transition-colors">
                    <span class="text-xs font-bold">{{ $mvt->created_at->format('d') }}</span>
                    <span class="text-[9px] uppercase font-bold">{{ $mvt->created_at->format('M') }}</span>
                </div>
                
                <!-- BL Info -->
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <a href="{{ route('tools.logistics.show', $mvt->bl->id) }}" class="font-mono text-xs font-bold text-neutral-400 hover:text-black hover:underline">
                            {{ $mvt->bl->bl_number }}
                        </a>
                        @if($mvt->warehouse)
                        <span class="text-[9px] font-bold text-neutral-500 bg-neutral-100 px-1.5 rounded uppercase tracking-wider">{{ $mvt->warehouse }}</span>
                        @endif
                    </div>
                    <span class="font-serif text-lg text-neutral-900">{{ $mvt->bl->client_name }}</span>
                    
                    @if($mvt->boxes_per_pallet && $mvt->boxes_per_pallet > 0 && $mvt->quantity > 0)
                        <div class="text-[10px] text-neutral-400 mt-1">
                            @php 
                                $boxes = $mvt->unit == 'm2' && $mvt->reference && strpos($mvt->reference, '|') !== false 
                                    ? $mvt->quantity / floatval(explode('|', $mvt->reference)[1]) 
                                    : ($mvt->unit == 'box' ? $mvt->quantity : 0);
                                $pallets = $boxes > 0 ? $boxes / $mvt->boxes_per_pallet : 0;
                            @endphp
                            @if($pallets > 0)
                                {{ number_format($pallets, 1) }} Palettes ({{ $mvt->boxes_per_pallet }} crt/pal)
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quantity & Status -->
            <div class="flex items-center gap-8 w-full md:w-auto justify-between md:justify-end">
                <div class="text-right">
                    <span class="block font-bold text-xl text-neutral-900">{{ floatval($mvt->quantity) }} <span class="text-xs font-normal text-neutral-500">{{ $mvt->unit }}</span></span>
                    
                    @if($mvt->status == 'delivered')
                        <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-wide bg-emerald-50 px-2 py-0.5 rounded">Chargé 100%</span>
                    @elseif($mvt->status == 'partial')
                        <span class="text-[9px] font-bold text-amber-600 uppercase tracking-wide bg-amber-50 px-2 py-0.5 rounded">
                            Partiel: {{ floatval($mvt->quantity_delivered) }}
                        </span>
                    @else
                        <span class="text-[9px] font-bold text-neutral-400 uppercase tracking-wide bg-neutral-100 px-2 py-0.5 rounded">En Attente</span>
                    @endif
                </div>

                <a href="{{ route('tools.logistics.show', $mvt->bl->id) }}" class="w-8 h-8 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-300 hover:border-black hover:text-black transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

        </div>
        @endforeach
    </div>

</div>
    <!-- JAVASCRIPT FOR ANALYTICS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data passed from controller
            const trends = @json($trends);
            const whData = @json($whChart);

            // 1. TREND CHART (Line)
            const ctxTrend = document.getElementById('trendChart').getContext('2d');
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: trends.labels,
                    datasets: [{
                        label: 'Volume Sortie',
                        data: trends.data,
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
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f3f4f6' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. WAREHOUSE CHART (Doughnut)
            if(whData.labels.length > 0) {
                const ctxWh = document.getElementById('whChart').getContext('2d');
                new Chart(ctxWh, {
                    type: 'doughnut',
                    data: {
                        labels: whData.labels,
                        datasets: [{
                            data: whData.data,
                            backgroundColor: ['#171717', '#E6AF5D', '#525252', '#d4d4d4', '#fbbf24'],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 10 } } }
                        },
                        cutout: '75%'
                    }
                });
            }
        });
    </script>

@endsection