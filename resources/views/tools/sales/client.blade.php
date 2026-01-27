@extends('layouts.app')

@section('title', $client->display_name)

@section('content')
<div class="max-w-6xl mx-auto pb-20 space-y-8">

    <!-- HEADER PROFILE -->
    <div class="bg-white rounded-[2rem] p-8 border border-neutral-100 shadow-sm relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[#E6AF5D]/5 opacity-50 scanner-lines"></div>

        <div class="relative z-10 flex flex-col md:flex-row gap-8 items-start">
            <!-- Avatar -->
            <div class="w-24 h-24 rounded-2xl bg-neutral-900 text-[#E6AF5D] flex items-center justify-center text-4xl font-serif font-bold shadow-lg">
                {{ substr($client->full_name, 0, 1) }}
            </div>

            <!-- Info -->
            <div class="flex-1">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                         @if($client->professional_category)
                            <div class="text-xs font-bold uppercase tracking-widest text-[#E6AF5D] mb-1">{{ $client->professional_category }}</div>
                         @elseif($client->company_name)
                            <div class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-1">{{ $client->company_name }}</div>
                        @endif
                        <h1 class="text-3xl font-serif font-bold text-neutral-900">{{ $client->display_name }}</h1>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('tools.sales.edit', $client->id) }}" class="px-4 py-2 bg-neutral-50 hover:bg-neutral-100 rounded-lg text-xs font-bold uppercase tracking-wide text-neutral-600 transition-colors">
                            Éditer
                        </a>
                        <a href="tel:{{ $client->phone }}" class="px-4 py-2 bg-[#E6AF5D] hover:bg-[#d49b4c] rounded-lg text-xs font-bold uppercase tracking-wide text-white shadow-md transition-colors">
                            Appeler
                        </a>
                    </div>
                </div>

                <div class="flex flex-wrap gap-6 mt-6 pt-6 border-t border-neutral-100/50">
                    <div>
                        <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Téléphone</span>
                        <span class="font-bold text-neutral-900">{{ $client->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Email</span>
                        <span class="font-bold text-neutral-900">{{ $client->email ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Potentiel</span>
                        <span class="font-bold text-[#E6AF5D]">{{ $client->potential_score }}%</span>
                    </div>
                    <div>
                        <span class="block text-[10px] text-neutral-400 uppercase tracking-wider">Intérêts</span>
                        <div class="flex gap-1 mt-0.5">
                            @forelse($client->products ?? [] as $tag)
                                <span class="px-1.5 py-0.5 bg-neutral-100 rounded text-[9px] uppercase font-bold text-neutral-500">{{ $tag }}</span>
                            @empty
                                <span class="text-xs text-neutral-300">-</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS & CONTENT -->
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- LEFT: PURCHASE HISTORY (LOGISTICS) -->
        <div class="w-full lg:w-2/3 space-y-6">
            
            <!-- NEW: RECOMMENDATIONS (Moved to Top) -->
            @if(isset($recommendations) && $recommendations->isNotEmpty())
            <div class="bg-neutral-900 rounded-3xl p-6 text-white relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-[#E6AF5D] rounded-full blur-3xl opacity-20"></div>
                <h3 class="font-bold text-sm mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#E6AF5D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                    Produits Suggérés (En Stock)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @foreach($recommendations as $recName)
                    <div class="bg-white/10 rounded-xl p-3 border border-white/5 hover:bg-white/20 transition-colors">
                        <p class="text-xs font-bold text-white truncate">{{ $recName }}</p>
                        <p class="text-[10px] text-[#E6AF5D] mt-1">Tendance & Dispo</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- CHARTS SECTION -->
            <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm p-6">
                <h3 class="font-bold text-lg text-neutral-900 mb-4">Fréquence d'Achat (Nombre de BLs)</h3>
                <div class="h-64 w-full">
                    <canvas id="volumeChart"></canvas>
                </div>
            </div>

            <!-- TOP ARTICLES -->
            <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-neutral-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-neutral-900">Articles les plus achetés</h3>
                </div>
                <div class="p-6">
                    @if(isset($topArticles) && $topArticles->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($topArticles as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-neutral-100 flex items-center justify-center text-xs font-bold text-neutral-500">
                                        {{ $loop->iteration }}
                                    </div>
                                    <span class="text-sm font-bold text-neutral-800">{{ $item['name'] }}</span>
                                </div>
                                <span class="text-sm font-mono font-bold text-[#E6AF5D]">{{ $item['total_qty'] }} <span class="text-xs text-neutral-400 font-sans">{{ $item['unit'] }}</span></span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-neutral-400 italic">Pas assez de données pour l'analyse.</p>
                    @endif
                </div>
            </div>

            <h3 class="font-bold text-lg text-neutral-900 flex items-center gap-2 mt-8">
                Historique Logistique
                <span class="px-2 py-0.5 bg-neutral-100 rounded-full text-xs text-neutral-500">{{ $relatedBls->count() }} BLs</span>
            </h3>

             @if($relatedBls->count() > 0)
                <div class="bg-white rounded-3xl border border-neutral-100 shadow-sm overflow-hidden divide-y divide-neutral-50">
                    @foreach($relatedBls as $bl)
                    <div class="p-6 hover:bg-neutral-50 transition-colors group">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="font-bold text-neutral-900 flex items-center gap-2">
                                    <span class="w-8 h-8 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-400 group-hover:bg-[#E6AF5D] group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                    </span>
                                    BL #{{ $bl->bl_number }}
                                </div>
                                <div class="text-xs text-neutral-400 mt-1 ml-10">{{ \Carbon\Carbon::parse($bl->date)->format('d F Y') }}</div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $bl->status == 'loaded' ? 'bg-emerald-50 text-emerald-600' : 'bg-neutral-100 text-neutral-500' }}">
                                {{ $bl->status }}
                            </span>
                        </div>
                        
                        <div class="ml-10">
                            <div class="flex flex-wrap gap-2">
                                @foreach($bl->articles as $article)
                                    <span class="text-xs border border-neutral-200 rounded px-2 py-1 text-neutral-600 bg-white">
                                        <span class="font-bold">{{ $article->quantity }}</span> {{ Str::limit($article->name, 30) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="bg-neutral-50 rounded-3xl p-12 text-center border border-dashed border-neutral-200">
                    <p class="text-sm text-neutral-400">Aucun bon de livraison trouvé pour ce client.</p>
                </div>
            @endif
        </div>

        <!-- RIGHT: NOTES & STATS (Sidebar) -->
        <div class="w-full lg:w-1/3 space-y-6">
            
            <!-- Notes (Editable) -->
            <div class="bg-[#FFFBF2] rounded-3xl p-6 border border-[#FDE68A]">
                <h3 class="text-xs font-bold uppercase tracking-widest text-[#D97706] mb-4">Notes Privées</h3>
                <form action="{{ route('tools.sales.note', $client->id) }}" method="POST">
                    @csrf
                    <textarea name="notes" rows="6" class="w-full bg-transparent border-none text-sm text-[#92400E] leading-relaxed italic focus:ring-0 p-0 placeholder-[#D97706]/50" placeholder="Ajoutez une note pour vous souvenir des détails importants...">{{ $client->notes }}</textarea>
                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="text-xs font-bold bg-[#D97706] text-white px-3 py-1.5 rounded-lg hover:bg-[#b45309] transition-colors">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Volume Card (Breakdown by Unit) -->
            <div class="bg-neutral-900 text-white rounded-3xl p-6 relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-[#E6AF5D] rounded-full blur-2xl opacity-20"></div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-neutral-400 mb-3">Volume Acheté (Par Unité)</h3>
                
                @if(isset($volumeByUnit) && $volumeByUnit->isNotEmpty())
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($volumeByUnit as $unit => $qty)
                        <div>
                             <span class="block text-xl font-serif font-bold text-[#E6AF5D]">{{ number_format($qty, 0, ',', ' ') }}</span>
                             <span class="text-xs text-neutral-400 font-bold uppercase">{{ $unit }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-neutral-500 italic">Aucune donnée.</p>
                @endif
            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('volumeChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Commandes (BLs)',
                data: @json($chartData['data']),
                backgroundColor: '#E6AF5D',
                borderRadius: 4,
                barThickness: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#f5f5f5' },
                    ticks: { precision: 0 } // No decimals for counts
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
