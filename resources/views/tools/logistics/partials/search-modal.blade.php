<!-- Product Search Modal -->
<div id="productSearchModal" class="hidden fixed inset-0 bg-neutral-900/90 z-[2000] flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white w-full max-w-2xl h-[80vh] rounded-2xl shadow-2xl flex flex-col relative overflow-hidden">
        
        <!-- Header -->
        <div class="p-6 border-b border-neutral-100 flex justify-between items-center bg-white z-10">
            <h3 class="font-serif text-xl font-bold">Sélectionner un Article</h3>
            <button onclick="closeProductSearch()" class="text-neutral-400 hover:text-black">✕</button>
        </div>

        <!-- Search Bar -->
        <div class="p-6 bg-neutral-50 border-b border-neutral-100">
            <input type="text" id="modalSearchInput" placeholder="Tapez pour rechercher..." 
                class="w-full text-lg bg-transparent border-b-2 border-neutral-200 focus:border-black focus:outline-none py-2 font-bold" autofocus>
        </div>

        <!-- Results List -->
        <div id="modalResults" class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <!-- Items injected here -->
            <div class="text-center text-neutral-400 mt-10">Commencez à taper...</div>
        </div>

    </div>
</div>

<script>
    let activeSearchRowId = null;
    const modal = document.getElementById('productSearchModal');
    const modalInput = document.getElementById('modalSearchInput');
    const modalResults = document.getElementById('modalResults');

    function openProductSearch(rowId) {
        activeSearchRowId = rowId;
        modal.classList.remove('hidden');
        modalInput.value = '';
        modalResults.innerHTML = '<div class="text-center text-neutral-400 mt-10">Tapez pour rechercher dans le catalogue...</div>';
        setTimeout(() => modalInput.focus(), 100);
        
        // Load initial "Recent" items if empty
        searchProducts(''); 
    }

    function closeProductSearch() {
        modal.classList.add('hidden');
        activeSearchRowId = null;
    }

    let searchTimer;
    modalInput.addEventListener('input', (e) => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => searchProducts(e.target.value), 300);
    });

    async function searchProducts(term) {
        try {
            const res = await fetch(`/tools/api/catalog-search?term=${encodeURIComponent(term)}`);
            const data = await res.json();

            modalResults.innerHTML = '';

            if(data.length === 0) {
                modalResults.innerHTML = '<div class="text-center text-neutral-400 mt-10">Aucun résultat trouvé.</div>';
                return;
            }

            data.forEach(item => {
                const el = document.createElement('div');
                el.className = 'p-4 rounded-xl border border-neutral-100 hover:border-[#E6AF5D] hover:shadow-md cursor-pointer transition-all bg-white group flex justify-between items-center';
                
                // Warehouse badge
                const wh = item.warehouse 
                    ? `<span class="bg-neutral-100 text-[9px] px-2 py-1 rounded text-neutral-500 uppercase font-bold tracking-wider">${item.warehouse}</span>`
                    : '';

                el.innerHTML = `
                    <div>
                        <div class="font-bold text-sm text-neutral-900 group-hover:text-[#E6AF5D] transition-colors">${item.name}</div>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="font-mono text-[10px] text-neutral-400 bg-neutral-50 px-1 rounded">${item.reference || 'NO-REF'}</span>
                            ${wh}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-bold uppercase text-neutral-500">${item.unit}</div>
                        ${item.conversion ? `<div class="text-[9px] text-neutral-400">Conv: ${item.conversion}</div>` : ''}
                    </div>
                `;

                el.addEventListener('click', () => {
                    selectProductFromModal(item);
                });

                modalResults.appendChild(el);
            });

        } catch(e) {
            console.error(e);
        }
    }

    function selectProductFromModal(item) {
        if(activeSearchRowId === null) return;

        const row = document.getElementById(`row-${activeSearchRowId}`);
        const nameInput = row.querySelector('.name-input');
        const refInput = row.querySelector('.ref-input');
        const unitSelect = row.querySelector('.unit-select');
        const whSelect = row.querySelector('.warehouse-select');
        const convInput = document.querySelector(`#conv_${activeSearchRowId}`);

        nameInput.value = item.name;
        refInput.value = item.reference;
        unitSelect.value = item.unit;
        if(item.warehouse) whSelect.value = item.warehouse;
        
        toggleConversion(activeSearchRowId);

        if(item.unit === 'box' && item.conversion) {
            convInput.value = item.conversion;
            gsap.fromTo(convInput, { color: 'red', scale: 1.1 }, { color: '#E6AF5D', scale: 1, duration: 0.5 });
        }

        calculateTotal(activeSearchRowId);
        closeProductSearch();
    }
    
    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') closeProductSearch();
    });
</script>
