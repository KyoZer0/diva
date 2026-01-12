@extends('layouts.app')

@section('title', 'Éditeur Étiquettes')
@section('page-title', '')

@section('content')

<!-- Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
    /* --- 1. LAYOUT & SCROLLING --- */
    /* Force full height and remove default padding to create an "App" feel */
    main { padding: 0 !important; max-width: 100% !important; height: calc(100vh - 73px); overflow: hidden; margin: 0 !important; }
    
    .studio-container {
        display: grid;
        grid-template-columns: 380px 1fr;
        height: 100%;
        background: #F9FAFB;
    }

    /* --- 2. CONTROLS (Sidebar) --- */
    .controls-panel {
        background: white;
        border-right: 1px solid #E5E5E5;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        padding: 2rem;
        z-index: 10;
        box-shadow: 10px 0 30px -10px rgba(0,0,0,0.02);
    }

    .input-group { margin-bottom: 1.25rem; }
    
    .input-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
        display: block;
    }

    .studio-input {
        width: 100%;
        background: #FAFAFA;
        border: 1px solid #E5E7EB;
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        font-family: 'Inter', sans-serif;
        font-size: 0.9rem;
        color: #111;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .studio-input:focus {
        background: #fff;
        border-color: #E6AF5D;
        box-shadow: 0 0 0 4px rgba(230, 175, 93, 0.1);
        outline: none;
    }
    
    .brand-btn {
        flex: 1;
        padding: 0.6rem;
        border: 1px solid #E5E7EB;
        background: white;
        border-radius: 0.6rem;
        font-size: 0.75rem;
        font-weight: 700;
        transition: all 0.2s;
    }
    .brand-btn:hover { border-color: #000; }
    .brand-btn.active { background: #000; color: #fff; border-color: #000; }

    /* --- 3. PREVIEW STAGE --- */
    .stage-panel {
        background-color: #F3F4F6;
        /* Dot Pattern */
        background-image: radial-gradient(#E5E7EB 1px, transparent 1px);
        background-size: 24px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* --- 4. THE PHYSICAL LABEL (What gets printed) --- */
    /* Dimensions for Standard Landscape A6/Postcard (~15x10cm) */
    .label-card {
        width: 600px;
        height: 380px;
        background: white;
        box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0,0,0,0.02);
        display: flex;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s;
    }

    /* Left Side: Identity & Price (Dark) */
    .lbl-sidebar {
        width: 35%;
        background: #09090b; /* Zinc 950 */
        color: white;
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    /* Gold Accent Line */
    .lbl-sidebar::after {
        content: '';
        position: absolute;
        top: 2rem; bottom: 2rem; right: 0;
        width: 2px;
        background: #E6AF5D;
    }

    /* Right Side: Details & QR (Light) */
    .lbl-main {
        width: 65%;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Typography inside Label */
    .lbl-brand { font-family: 'Playfair Display', serif; font-weight: 900; letter-spacing: -0.02em; line-height: 1; }
    .lbl-price-num { font-family: 'Playfair Display', serif; font-weight: 700; line-height: 1; }
    .lbl-ref { font-family: 'JetBrains Mono', monospace; font-weight: 500; letter-spacing: -0.03em; }
    .lbl-name { font-family: 'Inter', sans-serif; font-weight: 500; line-height: 1.2; }

    /* Print Logic */
    @media print {
        @page { size: landscape; margin: 0; }
        body * { visibility: hidden; }
        .label-card, .label-card * { visibility: visible; }
        .label-card {
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(1) !important;
            box-shadow: none;
            border: none;
            width: 100%; height: 100%; /* Fill paper */
            max-width: 15cm; max-height: 10cm; /* Limit to A6 size context */
        }
        .controls-panel, header, nav { display: none; }
        /* Force background colors print */
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>

<div class="studio-container">
    
    <!-- LEFT: EDITOR -->
    <div class="controls-panel">
        
        <!-- Header -->
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('tools.index') }}" class="w-8 h-8 flex items-center justify-center rounded-full bg-neutral-100 text-neutral-500 hover:bg-black hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="font-bold text-lg text-neutral-900 leading-none">Éditeur</h1>
                <span class="text-[10px] text-neutral-400 uppercase tracking-wider font-bold">Label Studio v2</span>
            </div>
        </div>

        <!-- Forms -->
        <div class="space-y-1">
            
            <!-- Brand -->
            <div class="input-group">
                <label class="input-label">Collection</label>
                <div class="flex gap-2 mb-2">
                    <button onclick="setBrand('DIVA', this)" class="brand-btn active">DIVA</button>
                    <button onclick="setBrand('IM', this)" class="brand-btn">IM</button>
                    <button onclick="setBrand('SG', this)" class="brand-btn">SG</button>
                </div>
                <input type="text" id="brandInput" class="studio-input" placeholder="Autre marque..." oninput="updateLabel()">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Référence</label>
                    <input type="text" id="ref" class="studio-input font-mono uppercase" placeholder="MRB-001" oninput="updateLabel()">
                </div>
                <div class="input-group">
                    <label class="input-label">Format (cm)</label>
                    <input type="text" id="format" class="studio-input" placeholder="60x120" oninput="updateLabel()">
                </div>
            </div>

            <div class="input-group">
                <label class="input-label">Désignation</label>
                <input type="text" id="designation" class="studio-input" placeholder="Marbre Statuario..." oninput="updateLabel()">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="input-group">
                    <label class="input-label">Prix (DH/m²)</label>
                    <input type="number" id="price" class="studio-input font-bold" placeholder="0" oninput="updateLabel()">
                </div>
                <div class="input-group">
                    <label class="input-label">Promo</label>
                    <input type="number" id="promo" class="studio-input text-red-600" placeholder="-" oninput="updateLabel()">
                </div>
            </div>

            <!-- Configuration -->
            <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                <div class="input-group">
                    <label class="input-label text-[#E6AF5D]">Numéro Réception (WhatsApp)</label>
                    <input type="text" id="phone" class="studio-input bg-white" value="212600000000" oninput="updateLabel()">
                    <p class="text-[9px] text-gray-400 mt-1.5">Le QR code redirige le client vers ce numéro pour demander un vendeur.</p>
                </div>
            </div>

        </div>

        <!-- Print Action -->
        <button onclick="window.print()" class="mt-auto w-full py-4 bg-black hover:bg-neutral-800 text-white rounded-xl font-bold text-xs uppercase tracking-widest transition-all shadow-xl hover:shadow-2xl flex items-center justify-center gap-3 group">
            <svg class="w-4 h-4 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Lancer l'Impression
        </button>
    </div>

    <!-- RIGHT: STAGE (Preview) -->
    <div class="stage-panel">
        
        <span class="absolute top-6 left-6 px-3 py-1 bg-white/80 backdrop-blur rounded-full text-[10px] font-bold text-neutral-400 uppercase tracking-widest border border-gray-200 shadow-sm">
            Aperçu 15 x 10 cm
        </span>

        <!-- THE LABEL CARD -->
        <div class="label-card" id="printable">
            
            <!-- Dark Sidebar (Brand & Price) -->
            <div class="lbl-sidebar">
                <!-- Brand -->
                <div>
                    <h2 id="lblBrand" class="lbl-brand text-4xl text-[#E6AF5D] mb-1">DIVA</h2>
                    <p class="text-[8px] text-neutral-500 uppercase tracking-[0.4em] font-bold ml-1">CERAMICA</p>
                </div>

                <!-- Price Block -->
                <div>
                    <div id="regularPriceGroup">
                        <span id="lblPrice" class="lbl-price-num text-6xl text-white">0</span>
                        <span class="text-xl text-neutral-500 font-light">DH</span>
                    </div>
                    
                    <!-- Promo Logic -->
                    <div id="promoPriceGroup" class="hidden">
                        <span id="lblOldPrice" class="text-sm text-neutral-500 line-through decoration-red-500 ml-1"></span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span id="lblPromo" class="lbl-price-num text-5xl text-[#E6AF5D]"></span>
                            <span class="text-xl text-white font-light">DH</span>
                        </div>
                    </div>
                    <p class="text-[8px] text-neutral-600 uppercase tracking-wide mt-3 ml-1">Prix TTC / Mètre Carré</p>
                </div>

                <!-- Footer Meta -->
                <div>
                    <p class="text-[8px] text-neutral-500 uppercase font-bold mb-1">Format</p>
                    <p id="lblFormat" class="text-xl font-mono text-white">--</p>
                </div>
            </div>

            <!-- Light Main (Info & QR) -->
            <div class="lbl-main">
                
                <!-- Product Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-1.5 py-0.5 bg-black text-white text-[9px] font-bold uppercase tracking-wider rounded-sm">Ref. Stock</span>
                        <div class="h-px bg-neutral-100 flex-1"></div>
                    </div>
                    
                    <h1 id="lblRef" class="lbl-ref text-5xl text-neutral-900 mb-4 tracking-tighter">---</h1>
                    <p id="lblDesignation" class="lbl-name text-2xl text-neutral-500 italic">Saisissez une désignation...</p>
                </div>

                <!-- Footer Action -->
                <div class="flex items-end justify-between border-t border-neutral-100 pt-4">
                    <div class="max-w-[220px]">
                        <p class="text-xs font-bold text-neutral-900 uppercase mb-1 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Scanner & Sauvegarder
                        </p>
                        <p class="text-[9px] text-neutral-400 leading-snug">
                            Gardez cette référence en mémoire et demandez un conseiller via WhatsApp.
                        </p>
                    </div>
                    <div class="bg-white p-1.5 border border-gray-100 rounded-lg shadow-sm">
                        <canvas id="qr-code" class="w-24 h-24"></canvas>
                    </div>
                </div>

            </div>
        </div>
        <!-- End Label -->

    </div>
</div>

<script>
    let selectedBrand = 'DIVA';

    function setBrand(name, btn) {
        selectedBrand = name;
        document.getElementById('brandInput').value = ''; 
        
        // Toggle Active Class
        document.querySelectorAll('.brand-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        updateLabel();
    }

    function updateLabel() {
        // 1. Inputs
        const customBrand = document.getElementById('brandInput').value;
        const brand = customBrand ? customBrand : selectedBrand;
        
        const ref = document.getElementById('ref').value || '---';
        const designation = document.getElementById('designation').value || 'Désignation...';
        const format = document.getElementById('format').value || '--';
        const price = document.getElementById('price').value || '0';
        const promo = document.getElementById('promo').value;
        const phone = document.getElementById('phone').value.replace(/[^0-9]/g, '') || '212600000000';

        // 2. DOM Updates
        document.getElementById('lblBrand').innerText = brand;
        document.getElementById('lblRef').innerText = ref;
        document.getElementById('lblDesignation').innerText = designation;
        document.getElementById('lblFormat').innerText = format;

        // 3. Price Logic
        if (promo && parseFloat(promo) > 0) {
            document.getElementById('regularPriceGroup').classList.add('hidden');
            document.getElementById('promoPriceGroup').classList.remove('hidden');
            document.getElementById('lblOldPrice').innerText = price + ' DH';
            document.getElementById('lblPromo').innerText = promo;
        } else {
            document.getElementById('regularPriceGroup').classList.remove('hidden');
            document.getElementById('promoPriceGroup').classList.add('hidden');
            document.getElementById('lblPrice').innerText = price;
        }

        // 4. QR Code Strategy (Lead Capture)
        const message = `*DEMANDE SHOWROOM*\n\nBonjour, je suis au magasin et je souhaite sauvegarder cette référence :\n\n📌 *${ref}* - ${designation}\n💰 Prix : ${promo || price} DH/m²\n\nMerci de m'assigner un conseiller pour vérifier le stock.`;
        
        const waLink = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

        new QRious({
            element: document.getElementById('qr-code'),
            value: waLink,
            size: 200,
            level: 'H'
        });
    }

    // Init
    updateLabel();
</script>

@endsection