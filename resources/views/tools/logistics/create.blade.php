@extends('layouts.app')

@section('title', 'Nouveau Chargement')
@section('content')

<!-- GSAP -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    /* Glass Effect */
    .glass-panel {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.05);
    }
    
    .glass-dropdown {
        background: #ffffff; /* Solid white for readability */
        border: 1px solid #f0f0f0;
        box-shadow: 0 20px 50px -12px rgba(0,0,0,0.25);
    }

    /* Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #E6AF5D; border-radius: 4px; }
</style>

<div class="max-w-7xl mx-auto pb-32">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-serif font-bold text-neutral-900">Nouveau Chargement</h1>
        </div>
        <a href="{{ route('tools.logistics.index') }}" class="text-xs font-bold uppercase tracking-widest text-neutral-400 hover:text-black">Retour</a>
    </div>

    <form action="{{ route('tools.logistics.store') }}" method="POST" id="blForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf

        <!-- LEFT: INFO (Static) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="glass-panel p-6 rounded-3xl">
                <h3 class="font-serif text-lg text-neutral-900 mb-4">Infos Générales</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold uppercase text-neutral-400">BL Numéro</label>
                        <input type="text" name="bl_number" id="blInput" class="w-full bg-neutral-50 border-neutral-200 rounded-xl p-3 font-bold" required>
                        <div id="blFeedback" class="mt-1"></div>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-neutral-400">BL Fournisseur</label>
                        <input type="text" name="supplier_ref" class="w-full bg-neutral-50 border-neutral-200 rounded-xl p-3" placeholder="Optionnel">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Nom Fournisseur</label>
                        <input type="text" name="supplier_name" class="w-full bg-neutral-50 border-neutral-200 rounded-xl p-3" placeholder="Optionnel">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Client</label>
                        <input type="text" name="client_name" class="w-full bg-neutral-50 border-neutral-200 rounded-xl p-3" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-neutral-400">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full bg-neutral-50 border-neutral-200 rounded-xl p-3" required>
                    </div>
                </div>
                
                <div class="mt-8">
                    <button type="submit" id="submitBtn" class="w-full py-4 bg-black text-white rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-[#E6AF5D] transition-colors">
                        Valider Dossier
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT: ARTICLES (Dynamic) -->
        <div class="lg:col-span-8">
            <!-- IMPORTANT: REMOVED overflow-hidden HERE -->
            <div class="glass-panel min-h-[600px] rounded-3xl flex flex-col relative z-0">
                
                <div class="p-6 border-b border-neutral-100 flex justify-between items-center">
                    <h2 class="font-serif text-xl">Articles</h2>
                    <button type="button" onclick="addRow()" class="px-4 py-2 bg-neutral-100 rounded-lg text-xs font-bold hover:bg-[#E6AF5D] hover:text-white transition-colors">
                        + Ajouter Ligne
                    </button>
                </div>

                <!-- Headers -->
                <div class="grid grid-cols-12 gap-4 px-6 py-2 bg-neutral-50 text-[9px] font-bold uppercase text-neutral-400">
                    <div class="col-span-4">Produit</div>
                    <div class="col-span-2">Dépôt</div>
                    <div class="col-span-2">Unité</div>
                    <div class="col-span-2">Qté</div>
                    <div class="col-span-1">Conv.</div>
                    <div class="col-span-1"></div>
                </div>

                <!-- Rows Container -->
                <!-- IMPORTANT: overflow-visible allowed here so dropdowns can pop out -->
                <div id="itemsContainer" class="p-4 space-y-4 flex-1"></div>
            </div>
        </div>
    </form>
</div>

@include('tools.logistics.partials.search-modal')

<script>
    let rowCount = 0;
    const container = document.getElementById('itemsContainer');

    // 1. Check BL Exists
    document.getElementById('blInput').addEventListener('blur', async function() {
        if(this.value.length < 3) return;
        try {
            const res = await fetch(`/tools/api/check-bl?bl_number=${this.value}`);
            const data = await res.json();
            const fb = document.getElementById('blFeedback');
            if(data.exists) {
                fb.innerHTML = '<span class="text-xs font-bold text-red-500">Existe déjà</span>';
                document.getElementById('submitBtn').disabled = true;
            } else {
                fb.innerHTML = '<span class="text-xs font-bold text-emerald-500">Disponible</span>';
                document.getElementById('submitBtn').disabled = false;
            }
        } catch(e) {}
    });

    // 2. Add Row Logic
    function addRow() {
        const div = document.createElement('div');
        // CSS: z-index is crucial here. We give higher z-index to newer rows or manage it dynamically
        // We use 'relative' so the absolute dropdown positions correctly
        div.className = 'grid grid-cols-12 gap-4 items-start relative bg-white p-2 rounded-xl border border-transparent hover:border-neutral-200 transition-colors';
        div.style.zIndex = 1000 - rowCount; // OLDER ROWS GET HIGHER Z-INDEX so dropdowns cover rows below
        div.id = `row-${rowCount}`;

        div.innerHTML = `
            <div class="col-span-4 relative">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" name="articles[${rowCount}][name]" 
                            class="name-input w-full border-b border-neutral-200 py-2 text-sm font-bold placeholder-neutral-300 focus:border-[#E6AF5D] focus:outline-none bg-transparent" 
                            placeholder="Rechercher..." autocomplete="off" required>
                        
                        <input type="text" name="articles[${rowCount}][reference]" 
                            class="ref-input w-full text-[10px] text-neutral-400 font-mono uppercase bg-transparent border-none p-0 focus:ring-0" 
                            placeholder="REF">

                        <!-- DROPDOWN CONTAINER -->
                        <div class="suggestions-box hidden absolute left-0 top-full mt-1 w-[150%] glass-dropdown rounded-xl shadow-2xl overflow-hidden z-50"></div>
                    </div>
                    <button type="button" onclick="openProductSearch(${rowCount})" class="text-neutral-300 hover:text-black pt-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </div>
            
            <div class="col-span-2">
                <select name="articles[${rowCount}][warehouse]" class="warehouse-select w-full text-xs border-none bg-transparent cursor-pointer focus:ring-0">
                    <option value="Mediouna">Mediouna</option>
                    <option value="S.M">S.M</option>
                    <option value="Lkhyayta">Lkhyayta</option>
                    <option value="Diva Ceramica">Diva Ceramica</option>
                </select>
            </div>

            <div class="col-span-2">
                <select name="articles[${rowCount}][unit]" onchange="toggleConversion(${rowCount})" 
                    class="unit-select w-full text-xs font-bold border-none bg-transparent cursor-pointer focus:ring-0">
                    <option value="box">Carton</option>
                    <option value="m2">m²</option>
                    <option value="piece">Pièce</option>
                    <option value="unity">Unité</option>
                </select>
            </div>

            <div class="col-span-2">
                <input type="number" step="0.01" id="input_qty_${rowCount}" oninput="calculateTotal(${rowCount})"
                    class="w-full text-sm font-bold text-center border-b border-neutral-100 py-2 focus:border-[#E6AF5D] focus:outline-none bg-transparent" placeholder="0">
            </div>

            <div class="col-span-1 relative">
                <input type="number" step="0.001" name="articles[${rowCount}][conversion]" id="conv_${rowCount}" oninput="calculateTotal(${rowCount})"
                    class="w-full text-xs font-mono text-center border-b border-neutral-100 py-2 focus:border-[#E6AF5D] focus:outline-none bg-transparent text-[#E6AF5D]" placeholder="m2/box">
                <div class="text-[9px] text-center text-neutral-300 mt-1" id="total_display_${rowCount}">0</div>
            </div>

            <input type="hidden" name="articles[${rowCount}][final_quantity]" id="final_qty_${rowCount}">

            <div class="col-span-1 text-center pt-2">
                <button type="button" onclick="this.closest('.grid').remove()" class="text-neutral-300 hover:text-red-500">×</button>
            </div>
        `;

        container.appendChild(div);
        
        // GSAP Enter
        gsap.from(div, { y: 10, opacity: 0, duration: 0.3 });

        setupAutocomplete(div.querySelector('.name-input'), rowCount);
        rowCount++;
    }

    // 3. Autocomplete Engine
    function setupAutocomplete(input, id) {
        const box = input.parentElement.querySelector('.suggestions-box');
        const refInput = document.querySelector(`#row-${id} .ref-input`);
        const convInput = document.querySelector(`#conv_${id}`);
        const unitSelect = document.querySelector(`#row-${id} .unit-select`);
        const warehouseSelect = document.querySelector(`#row-${id} .warehouse-select`);
        
        let timer;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            const term = this.value;

            if(term.length < 2) {
                gsap.to(box, { opacity: 0, duration: 0.2, onComplete: () => box.classList.add('hidden') });
                return;
            }

            timer = setTimeout(async () => {
                try {
                    const res = await fetch(`/tools/api/catalog-search?term=${encodeURIComponent(term)}`);
                    if(!res.ok) throw new Error('Network error');
                    
                    const data = await res.json();

                    box.innerHTML = '';
                    
                    if(data.length > 0) {
                        box.classList.remove('hidden');
                        box.style.opacity = 0; // Reset for animation
                        gsap.to(box, { opacity: 1, duration: 0.2 });
                        
                        data.forEach((item, idx) => {
                            const el = document.createElement('div');
                            el.className = 'px-4 py-3 hover:bg-[#E6AF5D] hover:text-white cursor-pointer border-b border-neutral-50 transition-colors flex justify-between group items-center';
                            
                            // Visual Badge for conversion
                            const badge = item.conversion 
                                ? `<span class="bg-neutral-100 text-neutral-500 text-[9px] px-1 rounded group-hover:bg-white/20 group-hover:text-white">${item.conversion}</span>` 
                                : '';
                            
                            // Warehouse hint
                            const wh = item.warehouse ? `<span class="text-[9px] italic opacity-50 ml-2">(${item.warehouse})</span>` : '';

                            el.innerHTML = `
                                <div>
                                    <div class="font-bold text-xs flex items-center">${item.name} ${wh}</div>
                                    <div class="text-[9px] opacity-60 font-mono">${item.reference || ''}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    ${badge}
                                    <span class="text-[10px] uppercase font-bold opacity-50">${item.unit === 'box' ? 'Crt' : item.unit}</span>
                                </div>
                            `;

                            // Click Action
                            el.addEventListener('click', () => {
                                input.value = item.name;
                                refInput.value = item.reference;
                                unitSelect.value = item.unit;
                                if(item.warehouse) warehouseSelect.value = item.warehouse;
                                
                                toggleConversion(id); // Set conversion input state

                                if(item.unit === 'box' && item.conversion) {
                                    convInput.value = item.conversion;
                                    // Highlight effect
                                    gsap.fromTo(convInput, { color: 'red', scale: 1.1 }, { color: '#E6AF5D', scale: 1, duration: 0.5 });
                                }

                                calculateTotal(id);
                                box.classList.add('hidden');
                            });

                            box.appendChild(el);
                            
                            // Stagger animation
                            gsap.from(el, { x: -10, opacity: 0, delay: idx * 0.05, duration: 0.2 });
                        });
                    } else {
                        gsap.to(box, { opacity: 0, duration: 0.2, onComplete: () => box.classList.add('hidden') });
                    }
                } catch(err) {
                    console.error("Fetch error:", err);
                    gsap.to(box, { opacity: 0, duration: 0.2, onComplete: () => box.classList.add('hidden') });
                }
            }, 300);
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if(!input.contains(e.target) && !box.contains(e.target)) {
                gsap.to(box, { opacity: 0, duration: 0.2, onComplete: () => box.classList.add('hidden') });
            }
        });
    }

    // 4. Calculation Math
    function toggleConversion(id) {
        const unit = document.querySelector(`#row-${id} .unit-select`).value;
        const convInput = document.querySelector(`#conv_${id}`);
        
        if(unit === 'box') {
            convInput.disabled = false;
            convInput.classList.remove('opacity-20');
        } else {
            convInput.disabled = true;
            convInput.value = '';
            convInput.classList.add('opacity-20');
        }
        calculateTotal(id);
    }

    function calculateTotal(id) {
        const unit = document.querySelector(`#row-${id} .unit-select`).value;
        const count = parseFloat(document.querySelector(`#input_qty_${id}`).value) || 0;
        const conversion = parseFloat(document.querySelector(`#conv_${id}`).value) || 0;
        const finalInput = document.querySelector(`#final_qty_${id}`);
        const display = document.querySelector(`#total_display_${id}`);

        let total = 0;

        if (unit === 'box' && conversion > 0) {
            total = count * conversion;
            display.innerText = `= ${total.toFixed(2)} m²`;
            display.classList.add('text-black', 'font-bold');
        } else {
            total = count;
            display.innerText = `= ${total.toFixed(2)} ${unit}`;
            display.classList.remove('text-black', 'font-bold');
        }

        finalInput.value = total.toFixed(2);
    }

    // Start with one row
    addRow();
</script>
@endsection