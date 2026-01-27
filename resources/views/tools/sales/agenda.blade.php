@extends('layouts.app')

@section('title', 'Stratégie & Notes - Cockpit Commercial')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-20">

    <!-- HEADER & NAV -->
    <div class="flex justify-between items-center">
        <a href="{{ route('tools.sales.index') }}" class="inline-flex items-center text-sm font-bold text-neutral-400 hover:text-neutral-900 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
        <h1 class="text-2xl font-serif font-bold text-neutral-900">Ma Stratégie</h1>
    </div>

    <!-- QUICK ADD BAR -->
    <div class="bg-white rounded-[2rem] p-2 pl-6 shadow-sm border border-neutral-100 flex items-center gap-4">
        <form action="{{ route('tools.sales.tasks.store') }}" method="POST" class="flex-1 flex gap-4">
            @csrf
            <div class="relative">
                <select name="type" class="appearance-none bg-neutral-50 px-4 py-3 pr-8 rounded-xl text-sm font-bold text-neutral-600 focus:ring-0 focus:bg-neutral-100 border-none cursor-pointer">
                    <option value="client">Client</option>
                    <option value="product">Produit</option>
                    <option value="memo">Note</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
            <input type="text" name="title" required placeholder="Qu'avez-vous en tête ?" class="flex-1 bg-transparent border-none text-neutral-900 placeholder-neutral-400 focus:ring-0 text-lg">
            <button type="submit" class="bg-neutral-900 text-white w-12 h-12 rounded-xl flex items-center justify-center hover:bg-[#E6AF5D] hover:text-black transition-colors shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </button>
        </form>
    </div>

    <!-- KANBAN BOARD -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
        
        <!-- ACTIVE COLUMN -->
        <div class="space-y-4">
            <h3 class="font-bold text-neutral-900 px-4 flex justify-between items-center">
                À Faire
                <span class="text-xs bg-neutral-100 text-neutral-500 px-2 py-1 rounded-full count-active">{{ $tasks->where('is_completed', false)->count() }}</span>
            </h3>
            
            <div id="todo-list" class="space-y-3 min-h-[200px]" data-status="pending">
                @foreach($tasks->where('is_completed', false) as $task)
                <div class="task-card bg-white p-5 rounded-2xl shadow-sm border border-neutral-100 cursor-move group hover:border-[#E6AF5D]/50 hover:shadow-md transition-all active:scale-[0.98]" data-id="{{ $task->id }}">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start gap-3">
                            <!-- Type Indicator -->
                            @php
                                $colors = ['client' => 'bg-blue-500', 'product' => 'bg-[#E6AF5D]', 'memo' => 'bg-neutral-400'];
                            @endphp
                            <div class="w-1.5 h-1.5 rounded-full mt-2 {{ $colors[$task->type] }}"></div>
                            
                            <div>
                                <p class="font-bold text-neutral-800 leading-snug">{{ $task->title }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 mt-1">{{ ucfirst($task->type) }}</p>
                            </div>
                        </div>

                        <!-- Drag Handle (Visual) -->
                        <div class="text-neutral-300 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- COMPLETED COLUMN -->
        <div class="space-y-4">
             <h3 class="font-bold text-neutral-400 px-4 flex justify-between items-center">
                Terminé
                <span class="text-xs bg-neutral-100 text-neutral-400 px-2 py-1 rounded-full count-done">{{ $tasks->where('is_completed', true)->count() }}</span>
            </h3>

            <div id="done-list" class="space-y-3 min-h-[200px]" data-status="completed">
                @foreach($tasks->where('is_completed', true) as $task)
                <div class="task-card bg-neutral-50 p-4 rounded-2xl border border-transparent cursor-move opacity-60 hover:opacity-100 transition-opacity" data-id="{{ $task->id }}">
                    <div class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="text-sm font-medium text-neutral-500 line-through">{{ $task->title }}</span>
                    </div>
                    
                    
                    <form action="{{ route('tools.sales.tasks.destroy', $task->id) }}" method="POST" class="mt-2 text-right">
                        @csrf @method('DELETE')
                        <button class="text-[10px] font-bold text-red-400 hover:text-red-600 uppercase tracking-widest">Supprimer</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<!-- SortableJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    const saveOrder = () => {
        const items = [];
        
        // Collect items from ToDo
        document.querySelectorAll('#todo-list .task-card').forEach((el, index) => {
            items.push({ id: el.dataset.id, position: index, status: 'pending' });
        });
        
        // Collect items from Done
        document.querySelectorAll('#done-list .task-card').forEach((el, index) => {
            items.push({ id: el.dataset.id, position: index, status: 'completed' });
        });

        // AJAX Save
        fetch('{{ route('tools.sales.tasks.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ items })
        });
        
        // Update Counters
        document.querySelector('.count-active').innerText = document.querySelectorAll('#todo-list .task-card').length;
        document.querySelector('.count-done').innerText = document.querySelectorAll('#done-list .task-card').length;
    };

    const config = {
        group: 'tasks',
        animation: 150,
        ghostClass: 'bg-[#E6AF5D]/10',
        onEnd: function(evt) {
            // Visual Updates for Done items (Adding/Removing styling)
            const item = evt.item;
            const isDone = item.closest('#done-list');
            
            if(isDone) {
                // Apply "Done" styling if moved to Done
                item.classList.add('bg-neutral-50', 'opacity-60');
                item.classList.remove('bg-white', 'shadow-sm', 'border-neutral-100');
            } else {
                // Apply "Active" styling if moved to Active
                item.classList.remove('bg-neutral-50', 'opacity-60');
                item.classList.add('bg-white', 'shadow-sm', 'border-neutral-100');
            }
            
            saveOrder();
        }
    };

    new Sortable(document.getElementById('todo-list'), config);
    new Sortable(document.getElementById('done-list'), config);
</script>
@endsection
