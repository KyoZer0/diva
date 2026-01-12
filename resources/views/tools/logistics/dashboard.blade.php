@extends('layouts.app')

@section('title', 'Logistique')
@section('content')

<style>
    /* Custom Folder CSS */
    .folder-tab {
        clip-path: polygon(0 0, 85% 0, 100% 100%, 0% 100%);
    }
    .paper-texture {
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg width='6' height='6' viewBox='0 0 6 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23000000' fill-opacity='0.02' fill-rule='evenodd'%3E%3Cpath d='M5 0h1v6H5V0zM0 5h6v1H0V5z'/%3E%3C/g%3E%3C/svg%3E");
    }
</style>

<div class="max-w-7xl mx-auto pb-20 pt-6">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
        <div>
            <h1 class="text-4xl font-serif text-neutral-900 font-medium tracking-tight">Logistique</h1>
            <p class="text-sm font-mono text-neutral-400 mt-2 uppercase tracking-widest">Flux & Opérations Journalières</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('tools.logistics.closing') }}" class="group relative px-6 py-3 bg-[#F5F5F0] text-neutral-600 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-neutral-900 hover:text-white transition-all overflow-hidden">
                <span class="relative z-10">Clôture Journée</span>
            </a>
            <a href="{{ route('tools.logistics.create') }}" class="px-6 py-3 bg-neutral-900 text-white rounded-full text-xs font-bold uppercase tracking-widest hover:bg-[#E6AF5D] hover:text-black transition-all shadow-lg flex items-center gap-2">
                <span>+</span> Nouveau Dossier
            </a>
        </div>
    </div>

    <!-- 1. PRIORITY TRAY (Backlog) -->
    @if($backlogBls->count() > 0)
    <div class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-1.5 bg-red-500 rounded-full animate-pulse"></div>
            <h3 class="font-serif text-xl text-neutral-900 italic">Reliquat Prioritaire</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($backlogBls as $bl)
            <a href="{{ route('tools.logistics.show', $bl->id) }}" class="group block relative bg-white border border-neutral-200 p-6 shadow-sm hover:shadow-xl hover:border-red-200 transition-all duration-300">
                <div class="absolute top-0 left-0 w-1 h-full bg-red-500 group-hover:w-full transition-all duration-500 opacity-10"></div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <span class="font-mono text-xs text-red-600 border border-red-100 bg-red-50 px-2 py-1">{{ $bl->bl_number }}</span>
                        <span class="text-[10px] font-bold uppercase text-neutral-400">{{ $bl->created_at->diffForHumans() }}</span>
                    </div>
                    <h4 class="font-serif text-2xl text-neutral-900 mb-1 group-hover:translate-x-1 transition-transform">{{ $bl->client_name }}</h4>
                    
                    <div class="mt-6 flex items-end justify-between">
                        <span class="text-xs font-bold text-neutral-400 uppercase tracking-wider">Incomplet</span>
                        <div class="w-16 h-1 bg-neutral-100">
                            <div class="h-full bg-red-500" style="width: {{ ($bl->articles->count() > 0 ? ($bl->articles->where('status', 'delivered')->count() / $bl->articles->count()) * 100 : 0) }}%"></div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 2. LIVE FEED (Minimalist List) -->
    <div class="mb-20">
        <div class="flex items-center justify-between border-b border-neutral-200 pb-4 mb-8">
            <h3 class="font-serif text-xl text-neutral-900 italic">En Cours Aujourd'hui</h3>
            <span class="font-mono text-xs text-neutral-400">{{ $todayBls->count() }} Dossiers</span>
        </div>

        @if($todayBls->count() > 0)
        <div class="space-y-4">
            @foreach($todayBls as $bl)
            <div class="group relative bg-white hover:bg-[#FAFAFA] transition-colors border-b border-neutral-100 last:border-0 pb-4">
                <a href="{{ route('tools.logistics.show', $bl->id) }}" class="flex flex-col md:flex-row items-center justify-between gap-6 py-4 px-2">
                    
                    <!-- Status Dot -->
                    <div class="flex items-center gap-6 w-full md:w-auto">
                        <div class="w-2 h-2 rounded-full {{ $bl->ui_state == 'alert' ? 'bg-amber-500' : ($bl->progress_pct == 100 ? 'bg-emerald-500' : 'bg-neutral-300') }}"></div>
                        <div>
                            <span class="block font-mono text-xs text-neutral-400 mb-1">{{ $bl->bl_number }}</span>
                            <span class="font-serif text-xl text-neutral-900">{{ $bl->client_name }}</span>
                        </div>
                    </div>

                    <!-- Visual Progress -->
                    <div class="flex-1 w-full md:w-auto md:px-12">
                        <div class="flex justify-between text-[10px] font-bold uppercase text-neutral-300 mb-2 tracking-widest">
                            <span>Progression</span>
                            <span>{{ round($bl->progress_pct) }}%</span>
                        </div>
                        <div class="w-full bg-neutral-100 h-px">
                            <div class="h-full bg-black transition-all duration-1000" style="width: {{ $bl->progress_pct }}%"></div>
                        </div>
                    </div>

                    <!-- Action -->
                    <div class="w-full md:w-auto text-right">
                        <span class="inline-block px-4 py-2 border border-neutral-200 rounded-full text-[10px] font-bold uppercase tracking-widest text-neutral-500 group-hover:bg-black group-hover:text-white group-hover:border-black transition-all">
                            Gérer
                        </span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-20 text-center bg-[#F9F9F9] rounded-sm">
            <p class="font-serif text-neutral-400 italic">Aucune activité pour le moment.</p>
        </div>
        @endif
    </div>

    <!-- 3. ARCHIVES (The Folders) -->
    <div>
        <h3 class="font-serif text-xl text-neutral-900 italic mb-8">Archives</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($archives as $archive)
            <a href="{{ route('tools.logistics.archives', ['date' => $archive->day]) }}" class="block group relative pt-4">
                <!-- Folder Tab -->
                <div class="folder-tab absolute top-0 left-0 w-20 h-6 bg-[#E5E5E5] group-hover:bg-[#E6AF5D] transition-colors z-0"></div>
                <!-- Folder Body -->
                <div class="relative z-10 bg-[#F0F0F0] p-6 h-32 flex flex-col justify-between shadow-sm group-hover:shadow-lg group-hover:-translate-y-1 transition-all duration-300 border-t border-white/50">
                    <span class="font-serif text-lg text-neutral-900">
                        {{ \Carbon\Carbon::parse($archive->day)->format('d M') }}
                    </span>
                    <div class="flex justify-between items-end border-t border-neutral-300/50 pt-2">
                        <span class="text-[10px] font-mono text-neutral-400">{{ \Carbon\Carbon::parse($archive->day)->format('Y') }}</span>
                        <span class="text-[10px] font-bold bg-white px-1.5 py-0.5 rounded text-neutral-900">{{ $archive->count }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

</div>
@endsection