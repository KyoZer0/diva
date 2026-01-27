@extends('layouts.app')

@section('title', 'AI Room Visualizer')

@section('content')
<!-- Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    /* Full Screen Fixes */
    /* We overwrite the layout margin/padding for this specific tool to be immersive */
    .main-content-wrapper { padding: 0 !important; } 

    /* Slider Styles */
    .slider-handle {
        width: 44px;
        height: 44px;
        background: white;
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        cursor: col-resize;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #E6AF5D;
        transition: transform 0.2s;
    }
    .slider-handle:hover { transform: translate(-50%, -50%) scale(1.1); }
    .slider-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #E6AF5D;
        transform: translateX(-50%);
        z-index: 40;
        pointer-events: none;
        box-shadow: 0 0 10px rgba(230, 175, 93, 0.5);
    }
    
    /* Hide scrollbar for drawer */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<!-- MAIN CONTAINER -->
<!-- Fixed positioning relative to viewport, but starting after sidebar on Desktop -->
<div x-data="visualizerApp()" class="fixed inset-0 md:left-64 z-10 bg-gray-900 overflow-hidden">

    <!-- CANVAS AREA (Full Screen) -->
    <div class="absolute inset-0 z-0 flex items-center justify-center bg-neutral-900">
        
        <!-- Loading State (Initial) -->
        <div x-show="!roomImage" class="text-center text-neutral-500 animate-pulse">
            <p class="text-xs uppercase tracking-widest">Chargement de la scène...</p>
        </div>

        <!-- Image Container -->
        <div x-show="roomImage" class="relative w-full h-full select-none">
            
            <!-- Original Image (Background) -->
            <img :src="roomImage" class="absolute inset-0 w-full h-full object-contain bg-black/50">

            <!-- Generated Image (Overlay) -->
            <div class="absolute inset-0 w-full h-full overflow-hidden flex items-center justify-center pointer-events-none">
                 <!-- Clip Path Implementation -->
                 <div class="relative w-full h-full object-contain" :style="`clip-path: polygon(0 0, ${sliderPosition}% 0, ${sliderPosition}% 100%, 0 100%)`">
                    <img x-show="generatedImage" :src="generatedImage" class="absolute inset-0 w-full h-full object-contain">
                 </div>
            </div>

             <!-- Slider Handle -->
             <div 
                x-show="generatedImage"
                class="absolute inset-y-0 z-50 cursor-col-resize"
                :style="`left: ${sliderPosition}%`"
                @mousedown="startDragging"
                @touchstart="startDragging"
            >
                <div class="slider-line"></div>
                <div class="slider-handle">
                    <svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
                </div>
            </div>

            <!-- AI Processing Overlay -->
            <div x-show="isLoading" class="absolute inset-0 z-50 bg-black/60 backdrop-blur-sm flex flex-col items-center justify-center transition-opacity">
                <div class="w-16 h-16 border-4 border-white/20 border-t-[#E6AF5D] rounded-full animate-spin mb-4"></div>
                <p class="text-white font-light tracking-[0.2em] animate-pulse">CREATING REALITY...</p>
            </div>

        </div>
    </div>


    <!-- BOTTOM DRAWER (Controls) -->
    <!-- Translates Y to show/hide. Max height constrained. -->
    <div 
        class="absolute bottom-0 left-0 right-0 bg-white/95 backdrop-blur-xl border-t border-white/20 shadow-[0_-10px_40px_rgba(0,0,0,0.2)] z-30 flex flex-col transition-transform duration-500 ease-out transform"
        :class="isDrawerOpen ? 'translate-y-0' : 'translate-y-[85%] hover:translate-y-[82%] cursor-pointer'"
        @click="!isDrawerOpen && (isDrawerOpen = true)"
        style="max-height: 80vh;"
    >
        <!-- Drawer Handle / Header -->
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 cursor-pointer" @click.stop="isDrawerOpen = !isDrawerOpen">
            <div class="flex items-center gap-3">
                <div class="w-10 h-1 rounded-full bg-gray-300 mx-auto absolute left-1/2 -translate-x-1/2 top-3 md:hidden"></div>
                <h1 class="font-serif text-xl text-neutral-900 pt-2 md:pt-0">Atelier</h1>
                <span class="text-[9px] uppercase tracking-widest text-[#E6AF5D] font-bold bg-[#E6AF5D]/10 px-2 py-1 rounded hidden md:inline-block">Studio</span>
            </div>
            
            <!-- Toggle Icon -->
            <button class="p-2 text-gray-400 hover:text-neutral-900 transition-transform duration-300" :class="isDrawerOpen ? 'rotate-180' : 'rotate-0'">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-8 no-scrollbar pb-32 md:pb-6" @click.stop>
            
            <!-- Grid Layout for Steps -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- 1. SCENE -->
                <div class="space-y-4">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 pl-1 border-l-2 border-[#E6AF5D] ml-1">01. Scène</label>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="room in presets.rooms" :key="room.id">
                            <button 
                                @click="selectRoom(room)"
                                class="relative aspect-square rounded-lg overflow-hidden border-2 transition-all hover:scale-105 duration-200"
                                :class="selectedRoomId === room.id ? 'border-[#E6AF5D] ring-2 ring-[#E6AF5D]/20' : 'border-transparent opacity-70 hover:opacity-100'"
                            >
                                <img :src="room.url" class="w-full h-full object-cover">
                            </button>
                        </template>
                        <!-- Upload -->
                        <button @click="$refs.roomInput.click()" class="aspect-square border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center text-gray-400 hover:text-[#E6AF5D] hover:border-[#E6AF5D] transition-all">
                            <svg class="w-5 h-5 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-[9px] uppercase font-bold">Photo</span>
                        </button>
                    </div>
                </div>

                <!-- 2. SURFACE & MATERIAL -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex flex-col md:flex-row gap-8">
                        
                        <!-- Surface -->
                        <div class="w-full md:w-1/3 space-y-4">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 pl-1 border-l-2 border-[#E6AF5D] ml-1">02. Surface</label>
                            <div class="flex bg-gray-100 p-1 rounded-lg">
                                <button @click="surface = 'Floor'" class="flex-1 py-2 text-xs font-bold uppercase rounded-md transition-all" :class="surface === 'Floor' ? 'bg-white shadow-sm text-black' : 'text-gray-400'">Sol</button>
                                <button @click="surface = 'Wall'" class="flex-1 py-2 text-xs font-bold uppercase rounded-md transition-all" :class="surface === 'Wall' ? 'bg-white shadow-sm text-black' : 'text-gray-400'">Mur</button>
                            </div>
                        </div>

                        <!-- Product -->
                        <div class="flex-1 space-y-4">
                            <label class="block text-[10px] font-bold uppercase tracking-widest text-neutral-400 pl-1 border-l-2 border-[#E6AF5D] ml-1">03. Produit</label>
                            
                            <!-- Horizontal Scroll for Products -->
                            <div class="flex gap-4 overflow-x-auto pb-2 no-scrollbar">
                                <template x-for="product in presets.products" :key="product.id">
                                    <div 
                                        @click="selectedProduct = product"
                                        class="min-w-[140px] p-2 rounded-xl border cursor-pointer transition-all hover:bg-gray-50 flex flex-col gap-2"
                                        :class="selectedProduct?.id === product.id ? 'border-[#E6AF5D] bg-[#E6AF5D]/5' : 'border-gray-200'"
                                    >
                                        <div class="aspect-video rounded-lg overflow-hidden bg-gray-200">
                                            <img :src="product.url" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold uppercase text-neutral-900 truncate" x-text="product.name"></p>
                                            <p class="text-[9px] text-neutral-500 truncate" x-text="product.desc"></p>
                                        </div>
                                    </div>
                                </template>
                                <!-- Upload Product -->
                                <button @click="$refs.productInput.click()" class="min-w-[100px] border border-dashed border-gray-300 rounded-xl flex flex-col items-center justify-center text-gray-400 hover:text-black hover:border-black transition-all">
                                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <span class="text-[9px] font-bold uppercase">Importer</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- GENERATE FOOTER -->
        <div class="p-4 border-t border-gray-100 bg-white" @click.stop>
            <div class="flex items-center justify-between gap-4">
                <div class="hidden md:block text-xs text-gray-400">
                    <span x-show="selectedProduct" x-text="'Sélection: ' + selectedProduct.name"></span>
                </div>
                <button 
                    @click="generate()"
                    class="flex-1 md:flex-none md:w-64 py-3 bg-neutral-900 text-white rounded-lg font-bold uppercase tracking-widest text-xs hover:bg-black transition-all disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg"
                    :disabled="!isValid || isLoading"
                >
                    <span x-show="!isLoading">Générer le rendu</span>
                    <svg x-show="isLoading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
            <p x-show="error" class="text-red-500 text-[9px] mt-2 text-center" x-text="error"></p>
        </div>
    </div>


    <!-- HIDDEN INPUTS -->
    <input x-ref="roomInput" type="file" @change="handleRoomUpload" accept="image/*" class="hidden">
    <input x-ref="productInput" type="file" @change="handleProductUpload" accept="image/*" class="hidden">

</div>

<script>
    function visualizerApp() {
        return {
            isDrawerOpen: true,
            presets: {
                rooms: [
                    { id: 1, name: 'Salon', url: 'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?q=80&w=1600' },
                    { id: 2, name: 'Bain', url: 'https://images.unsplash.com/photo-1552321554-5fefe8c9ef14?q=80&w=1600' },
                    { id: 3, name: 'Cuisine', url: 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?q=80&w=1600' },
                    { id: 4, name: 'Lobby', url: 'https://images.unsplash.com/photo-1600607687644-c7171b42498f?q=80&w=1600' },
                ],
                products: [
                    { id: 'p1', name: 'Marbre Blanc', desc: 'White polished marble with grey veins', url: 'https://images.unsplash.com/photo-1618220252344-836e3e94f421?q=80&w=300' },
                    { id: 'p2', name: 'Béton Industriel', desc: 'Raw grey concrete matte finish', url: 'https://images.unsplash.com/photo-1517646287270-a5a9ca602e5c?q=80&w=300' },
                    { id: 'p3', name: 'Chêne Clair', desc: 'Natural light oak wood floor', url: 'https://images.unsplash.com/photo-1516455590571-18256e5bb9ff?q=80&w=300' },
                ]
            },
            roomImage: null,
            selectedRoomId: null,
            selectedProduct: null,
            surface: 'Floor',
            generatedImage: null,
            isLoading: false,
            error: null,
            sliderPosition: 50,
            isDragging: false,

            get isValid() { return this.roomImage && this.selectedProduct && this.surface; },

            selectRoom(room) {
                this.selectedRoomId = room.id;
                this.generatedImage = null;
                // Use higher res for full screen
                this.convertUrlToBase64(room.url, (b64) => this.roomImage = b64);
            },

            handleRoomUpload(e) {
                const file = e.target.files[0];
                if(file) this.readImage(file, (res) => { this.roomImage = res; this.selectedRoomId = null; this.generatedImage = null; });
            },

            handleProductUpload(e) {
                const file = e.target.files[0];
                if(!file) return;
                const desc = prompt("Description:", "Texture");
                if(!desc) return;
                this.readImage(file, (res) => {
                    const p = { id: 'c_'+Date.now(), name: 'Custom', desc, url: res };
                    this.presets.products.unshift(p);
                    this.selectedProduct = p;
                });
            },

            readImage(file, cb) {
                const r = new FileReader();
                r.onload = (e) => cb(e.target.result);
                r.readAsDataURL(file);
            },

            async generate() {
                if(!this.isValid) return;
                this.isLoading = true;
                this.isDrawerOpen = false; // Hide drawer
                this.error = null;
                
                try {
                    const res = await fetch('{{ route('tools.visualizer.generate') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ room_image: this.roomImage, surface: this.surface, product_description: this.selectedProduct.desc })
                    });
                    const data = await res.json();
                    if(data.success) {
                        this.generatedImage = data.image_url;
                        this.sliderPosition = 50;
                    } else {
                        this.isDrawerOpen = true; // Show drawer
                        throw new Error(data.message.includes('429') ? 'Quota API dépassé (Free Tier)' : data.message);
                    }
                } catch(e) {
                    this.error = e.message;
                    this.isDrawerOpen = true;
                } finally {
                    this.isLoading = false;
                }
            },

            convertUrlToBase64(url, callback) {
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = this.naturalWidth;
                    canvas.height = this.naturalHeight;
                    canvas.getContext('2d').drawImage(this,0,0);
                    callback(canvas.toDataURL('image/jpeg'));
                };
                img.src = url;
            },

            init() {
                // DEFAULT SELECTION
                if(this.presets.rooms.length > 0) {
                    this.selectRoom(this.presets.rooms[0]);
                }
                
                // DRAG
                window.addEventListener('mouseup', () => this.isDragging = false);
                window.addEventListener('touchend', () => this.isDragging = false);
                window.addEventListener('mousemove', (e) => this.drag(e));
                window.addEventListener('touchmove', (e) => this.drag(e));
            },
            
            drag(e) {
                if(!this.isDragging) return;
                const x = e.clientX || e.touches[0].clientX;
                const w = window.innerWidth;
                const offset = w > 768 ? 256 : 0; // Account for sidebar width if needed for precise slider? 
                // Actually visualizer is `fixed`. 
                // If it starts at left:0, simple math.
                // If it starts at left:256px (on md), we need to adjust calculating if clientX is relative to viewport.
                // clientX is viewport. 
                // The container starts at 256px on MD.
                // So valid X range is [256, W]. 
                // Width of container is W - 256.
                // localX = x - 256.
                
                let startX = 0;
                let containerW = w;
                
                if(w >= 768) { // Tailwind MD
                     startX = 256; // 16rem
                     containerW = w - 256;
                }
                
                let localX = x - startX;
                let percent = (localX / containerW) * 100;
                
                this.sliderPosition = Math.max(0, Math.min(100, percent));
            },
            
            startDragging(e) { this.isDragging = true; }
        }
    }
</script>
@endsection

