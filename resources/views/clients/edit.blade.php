@extends('layouts.app')

@section('title', 'Modifier Client')
@section('page-title', 'Modifier le dossier client')
@section('page-description', 'Mise à jour des informations pour ' . $client->full_name)

@section('content')

    {{-- Pre-processing data to handle JSON strings from DB safely --}}
    @php
        // --- PROCESS PRODUCTS ---
        $dbProducts = $client->products;
        
        // If Model doesn't cast to array, it comes as a JSON string. We decode it manually.
        if (is_string($dbProducts)) {
            $dbProducts = json_decode($dbProducts, true);
        }
        // Ensure it is an array (handles null or failed decode)
        if (!is_array($dbProducts)) {
            $dbProducts = [];
        }

        // Get either old input (if validation failed) or the DB data
        $currentProducts = old('products', $dbProducts);
        
        // Double check strict array type for in_array usage later
        if (!is_array($currentProducts)) {
            $currentProducts = [];
        }

        // Extract "Autres" value
        $productOtherValue = '';
        $productOtherChecked = false;
        
        foreach($currentProducts as $p) {
            if(is_string($p) && str_starts_with($p, 'Autres: ')) {
                $productOtherChecked = true;
                $productOtherValue = str_replace('Autres: ', '', $p);
                break;
            }
        }

        // --- PROCESS STYLES ---
        $dbStyles = $client->style;

        // Decode if string
        if (is_string($dbStyles)) {
            $dbStyles = json_decode($dbStyles, true);
        }
        // Ensure array
        if (!is_array($dbStyles)) {
            $dbStyles = [];
        }

        // Get old input or DB data
        $currentStyles = old('style', $dbStyles);
        
        // Ensure strict array
        if (!is_array($currentStyles)) {
            $currentStyles = [];
        }

        // Extract "Autres" value
        $styleOtherValue = '';
        $styleOtherChecked = false;

        foreach($currentStyles as $s) {
            if(is_string($s) && str_starts_with($s, 'Autres: ')) {
                $styleOtherChecked = true;
                $styleOtherValue = str_replace('Autres: ', '', $s);
                break;
            }
        }
    @endphp

	<!-- Client Form -->
	<div class="bg-white p-8 rounded-2xl shadow-sm border border-neutral-200 mb-10">
		<form action="{{ route('clients.update', $client->id) }}" method="POST" id="clientForm">
			@csrf
            @method('PUT')

			<div class="max-w-3xl mx-auto space-y-8">
				
				<!-- Type de client -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Type de client *</label>
					<div class="grid grid-cols-2 gap-4">
						<label class="custom-radio-card">
							<input type="radio" name="client_type" value="particulier" required class="hidden radio-input"
                                {{ old('client_type', $client->client_type) == 'particulier' ? 'checked' : '' }}>
							<div class="radio-card-content">
								<svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
								</svg>
								<span class="font-medium">Particulier</span>
							</div>
						</label>
						<label class="custom-radio-card">
							<input type="radio" name="client_type" value="professionnel" class="hidden radio-input"
                                {{ old('client_type', $client->client_type) == 'professionnel' ? 'checked' : '' }}>
							<div class="radio-card-content">
								<svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
								</svg>
								<span class="font-medium">Professionnel</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Nom complet -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Nom complet *</label>
					<input type="text" name="full_name" id="full_name" required
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: Ahmed Benali"
                        value="{{ old('full_name', $client->full_name) }}">
				</div>

				<!-- Company fields (hidden by default) -->
				<!-- Company fields (hidden by default) -->
                @php
                    $isPro = old('client_type', $client->client_type) === 'professionnel';
                    $category = old('professional_category', $client->professional_category);
                @endphp
				<div id="company_fields" style="{{ $isPro ? 'display: block;' : 'display: none;' }}" class="space-y-6">
					<div class="form-field">
						<label class="block text-sm font-medium text-neutral-700 mb-2">Nom de l'entreprise</label>
						<input type="text" name="company_name" id="company_name"
							class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
							placeholder="Ex: Deco Design SARL"
                            value="{{ old('company_name', $client->company_name) }}">
					</div>

                    <div class="form-field">
						<label class="block text-sm font-medium text-neutral-700 mb-3">Catégorie Professionnelle</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="professional_category" value="revendeur" class="peer hidden" {{ $category == 'revendeur' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border border-neutral-300 text-center text-sm font-medium text-neutral-600 transition-all peer-checked:border-[#E6AF5D] peer-checked:bg-[#E6AF5D] peer-checked:text-white hover:bg-neutral-50">
                                    Revendeur
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="professional_category" value="architecte" class="peer hidden" {{ $category == 'architecte' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border border-neutral-300 text-center text-sm font-medium text-neutral-600 transition-all peer-checked:border-[#E6AF5D] peer-checked:bg-[#E6AF5D] peer-checked:text-white hover:bg-neutral-50">
                                    Architecte
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="professional_category" value="promoteur" class="peer hidden" {{ $category == 'promoteur' ? 'checked' : '' }}>
                                <div class="px-4 py-3 rounded-xl border border-neutral-300 text-center text-sm font-medium text-neutral-600 transition-all peer-checked:border-[#E6AF5D] peer-checked:bg-[#E6AF5D] peer-checked:text-white hover:bg-neutral-50">
                                    Promoteur
                                </div>
                            </label>
                        </div>
					</div>
				</div>

				<!-- Téléphone -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Téléphone *</label>
					<input type="tel" name="phone" id="phone" required
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: 0612345678"
                        value="{{ old('phone', $client->phone) }}">
				</div>

				<!-- Email (optional) -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Email <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<input type="email" name="email" id="email"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: client@email.com"
                        value="{{ old('email', $client->email) }}">
				</div>

				<!-- Smart City Picker (Edit Mode) -->
				<div class="form-field" x-data="cityPicker('{{ old('city', $client->city) }}')">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Ville <span class="text-neutral-400 text-xs">(optionnel)</span></label>
                    <div class="relative z-50">
					    <input type="text" name="city" id="cityInput"
						    class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all pl-10"
                            placeholder="Rechercher une ville..."
                            autocomplete="off"
                            x-model="search"
                            @input="filterCities"
                            @focus="open = true"
                            @click.away="open = false"
                            @keydown.escape="open = false"
                            @keydown.arrow-down.prevent="highlightNext"
                            @keydown.arrow-up.prevent="highlightPrev"
                            @keydown.enter.prevent="selectHighlighted">
                        
                        <!-- Icon -->
                        <div class="absolute left-3 top-3.5 text-neutral-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        
                        <!-- Dropdown -->
                        <div x-show="open && (filteredCities.length > 0 || search.length > 0)" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 right-0 mt-2 bg-white border border-neutral-100 rounded-xl shadow-2xl max-h-64 overflow-y-auto custom-scrollbar z-[100] ring-1 ring-black/5"
                             style="display: none;">
                            
                            <!-- Suggestions -->
                            <template x-for="(city, index) in filteredCities" :key="index">
                                <button type="button" 
                                    class="w-full text-left px-4 py-3 text-sm hover:bg-neutral-50 flex items-center justify-between group transition-colors border-b border-neutral-50 last:border-0"
                                    :class="{'bg-neutral-50': index === highlightedIndex}"
                                    @click="selectCity(city)">
                                    <span class="font-medium text-neutral-700 group-hover:text-black" x-text="city"></span>
                                </button>
                            </template>
                            
                            <!-- No Results -->
                            <div x-show="filteredCities.length === 0 && search.length > 1" class="px-4 py-4 text-sm text-neutral-500 text-center">
                                <p class="mb-2">Aucun résultat pour "<span x-text="search" class="font-bold"></span>"</p>
                                <button type="button" @click="open = false" class="text-[#E6AF5D] text-xs font-bold hover:underline uppercase tracking-wide">Utiliser cette ville</button>
                            </div>
                        </div>
                    </div>
				</div>

                <script>
                    function cityPicker(initialValue = '') {
                        return {
                            search: initialValue,
                            open: false,
                            cities: [
    'Afourar', 'Agadir', 'Aghbala', 'Aghbalou', 'Agdz', 'Agourai', 'Ain Bni Mathar', 'Ain Cheggag', 'Ain Dorij', 'Ain El Aouda', 'Ain Erreggada', 'Ain Harrouda', 'Ain Jemaa', 'Ain Karma', 'Ain Leuh', 'Ain Taoujdate', 'Ait Baha', 'Ait Boubidmane', 'Ait Daoud', 'Ait Ishaq', 'Ait Melloul', 'Ait Ourir', 'Ait Youssef Ou Ali', 'Ajdir', 'Akchour', 'Akka', 'Aklim', 'Al Aroui', 'Al Hoceïma', 'Alnif', 'Amizmiz', 'Aoufous', 'Arbaoua', 'Arfoud', 'Assa', 'Assahrij', 'Assilah', 'Azemmour', 'Azilal', 'Azrou', 'Aïn Harrouda', 'Aïn Leuh',
    'Bab Berred', 'Bab Taza', 'Bejaâd', 'Ben Ahmed', 'Ben Guerir', 'Ben Sergao', 'Ben Taieb', 'Ben Yakhlef', 'Beni Ayat', 'Benslimane', 'Berkane', 'Berrechid', 'Bhalil', 'Bin elouidane', 'Biougra', 'Bir Jdid', 'Bni Ansar', 'Bni Bouayach', 'Bni Chiker', 'Bni Drar', 'Bni Hadifa', 'Bni Tadjite', 'Bouanane', 'Bouarfa', 'Boudnib', 'Boufakrane', 'Bouguedra', 'Bouhdila', 'Bouizakarne', 'Boujdour', 'Boujniba', 'Boulanouare', 'Boulemane', 'Boumalne-Dadès', 'Boumia', 'Bouskoura', 'Bouznika', 'Bradia', 'Brikcha', 'Bzou', 'Béni Mellal',
    'Casablanca', 'Chefchaouen', 'Chemaia', 'Chichaoua',
    'Dakhla', 'Dar Bni Karrich', 'Dar Bouazza', 'Dar Chaoui', 'Dar El Kebdani', 'Dar Gueddari', 'Dar Ould Zidouh', 'Dcheira El Jihadia', 'Debdou', 'Demnate', 'Deroua', 'Douar Kannine', 'Dra\'a', 'Drargua', 'Driouch',
    'Echemmaia', 'El Aïoun Sidi Mellouk', 'El Borouj', 'El Gara', 'El Guerdane', 'El Hajeb', 'El Hanchane', 'El Jadida', 'El Kelaâ des Sraghna', 'El Ksiba', 'El Marsa', 'El Menzel', 'El Ouatia', 'Elkbab', 'Ellaboukhate', 'Er-Rich', 'Errachidia', 'Es-Semara', 'Essaouira',
    'Fam El Hisn', 'Farkhana', 'Figuig', 'Fnideq', 'Foum Jamaa', 'Foum Zguid', 'Fquih Ben Salah', 'Fès',
    'Ghafsai', 'Ghmate', 'Goulmima', 'Gourrama', 'Guelmim', 'Guercif', 'Gueznaia', 'Guigou', 'Guisser',
    'Had Bouhssoussen', 'Had Kourt', 'Haj Kaddour', 'Harhoura', 'Hattane', 'Houara',
    'Ifrane', 'Ighoud', 'Ighrem', 'Ignachawn', 'Imilchil', 'Imintanoute', 'Imouzzer Kandar', 'Imouzzer Marmoucha', 'Imzouren', 'Inezgane', 'Irherm', 'Issaguen', 'Itzer',
    'Jamâat Shaim', 'Jaâdar', 'Jebha', 'Jerada', 'Jorf', 'Jorf El Melha', 'Jorf Lasfar',
    'Karia', 'Karia Ba Mohamed', 'Kariat Arekmane', 'Kasba Tadla', 'Kassita', 'Kattara', 'Kehf Nsour', 'Kelaat-M\'Gouna', 'Kenitra', 'Kerouna', 'Kerrouchen', 'Khemis Zemamra', 'Khemisset', 'Khenichet', 'Khouribga', 'Khémis Sahel', 'Khénifra', 'Ksar El Kebir', 'Ksar El Majaz', 'Ksar Sghir',
    'Laataouia', 'Laayoune', 'Lagouira', 'Lakhsas', 'Lahraouyine', 'Lalla Mimouna', 'Larache', 'Lbir Jdid', 'Loualidia', 'Loulad', 'Lqliâa',
    'M\'diq', 'M\'haya', 'M\'rirt', 'Mabrouk', 'Madagh', 'Maghama', 'Marrakech', 'Martil', 'Massa', 'Matmata', 'Mechra Bel Ksiri', 'Mehdia', 'Meknès', 'Melloussa', 'Midar', 'Midelt', 'Missour', 'Mohammedia', 'Moqrisset', 'Moulay Abdallah', 'Moulay Ali Cherif', 'Moulay Bouazza', 'Moulay Bousselham', 'Moulay Brahim', 'Moulay Driss Zerhoun', 'Moulay Yacoub', 'Moussaoua', 'MyAliCherif', 'Mzouda', 'Médiouna',
    'N\'Zalat Bni Amar', 'Nador', 'Naima',
    'Oualidia', 'Ouaouizeght', 'Ouarzazate', 'Ouazzane', 'Oued Amlil', 'Oued Heimar', 'Oued Laou', 'Oued Rmel', 'Oued Zem', 'Oujda', 'Oulad Abbou', 'Oulad Amrane', 'Oulad Ayad', 'Oulad Berhil', 'Oulad Frej', 'Oulad Ghadbane', 'Oulad H\'Riz Sahel', 'Oulad M\'Barek', 'Oulad M\'rah', 'Oulad Said', 'Oulad Sidi Ben Daoud', 'Oulad Teïma', 'Oulad Yaich', 'Oulad Zmam', 'Oulmès', 'Ounagha', 'Outat El Haj',
    'Point Cires',
    'Rabat', 'Ras El Aïn', 'Ras El Ma', 'Ribate El Kheir', 'Rissani', 'Rommani',
    'Sabaa Aiyoun', 'Safi', 'Salé', 'Sebt Gzoula', 'Sebt Jahjouh', 'Sefrou', 'Selouane', 'Settat', 'Sid L\'Mokhtar', 'Sid Zouin', 'Sidi Abdallah Ghiat', 'Sidi Addi', 'Sidi Ahmed', 'Sidi Ali Ban Hamdouche', 'Sidi Allal El Bahraoui', 'Sidi Allal Tazi', 'Sidi Bennour', 'Sidi Bou Othmane', 'Sidi Boubker', 'Sidi Bouknadel', 'Sidi Bouzid', 'Sidi Ifni', 'Sidi Jaber', 'Sidi Kacem', 'Sidi Lyamani', 'Sidi Mohamed ben Abdallah', 'Sidi Rahhal', 'Sidi Rahhal Chatai', 'Sidi Slimane', 'Sidi Slimane Echcharaa', 'Sidi Smail', 'Sidi Taibi', 'Sidi Yahya El Gharb', 'Sidi Yahya Zaer', 'Skhirate', 'Skhour Rehamna', 'Skoura', 'Smara', 'Souk El Arbaa', 'Souk Sebt Oulad Nemma', 'Stehat', 'Séfia',
    'Tabounte', 'Tafetachte', 'Tafraout', 'Taghjijt', 'Tahannaout', 'Tahla', 'Tainaste', 'Talmest', 'Taliouine', 'Talsint', 'Tamallalt', 'Tamanar', 'Tamansourt', 'Tamassint', 'Tameslouht', 'Tan-Tan', 'Tanger', 'Taounate', 'Taourirt', 'Tararget', 'Taroudant', 'Tata', 'Taza', 'Taïnaste', 'Temsia', 'Tendrara', 'Thar Es-Souk', 'Tiddas', 'Tiflet', 'tifnit', 'Tighassaline', 'Tighza', 'Tikiouine', 'Timahdite', 'Tinejdad', 'Tinghir', 'Tissa', 'Tit Mellil', 'Tiznit', 'Tiztoutine', 'Touarga', 'Touima', 'Touissit', 'Toulal', 'Toundoute', 'Tounfite', 'Témara', 'Tétouan',
    'Youssoufia',
    'Zag', 'Zagora', 'Zaio', 'Zaouiat Cheikh', 'Zaïda', 'Zeghanghane', 'Zemamra', 'Zirara', 'Zoumi', 'Zrarda'
],
                            filteredCities: [],
                            highlightedIndex: -1,
                            
                            init() {
                                // Initialize filter on load if value exists
                                if(this.search) this.filterCities();
                            },

                            filterCities() {
                                if (this.search === '') {
                                    this.filteredCities = [];
                                    return;
                                }
                                const query = this.search.toLowerCase();
                                this.filteredCities = this.cities.filter(city => 
                                    city.toLowerCase().includes(query)
                                );
                                this.open = true;
                                this.highlightedIndex = -1;
                            },
                            selectCity(city) {
                                this.search = city;
                                this.open = false;
                            },
                            highlightNext() {
                                if (this.highlightedIndex < this.filteredCities.length - 1) this.highlightedIndex++;
                                else if (this.highlightedIndex === -1 && this.filteredCities.length > 0) this.highlightedIndex = 0;
                            },
                            highlightPrev() {
                                if (this.highlightedIndex > 0) this.highlightedIndex--;
                            },
                            selectHighlighted() {
                                if (this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredCities.length) {
                                    this.selectCity(this.filteredCities[this.highlightedIndex]);
                                }
                            }
                        }
                    }
                </script>

				<hr class="border-neutral-200">

				<!-- Produits d'intérêt -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Produits d'intérêt</label>
					<div class="grid grid-cols-2 gap-3 mb-3">
                        @foreach([
                            'carrelage_sol' => 'Carrelage sol',
                            'carrelage_mural' => 'Carrelage mural',
                            'sanitaire' => 'Sanitaire',
                            'meubles_salle_de_bain' => 'Meubles de salle de bain',
                            'robinets' => 'Robinets',
                            'revetements_exterieurs' => 'Revêtements extérieurs'
                        ] as $val => $label)
						<label class="custom-checkbox-card">
							<input type="checkbox" name="products[]" value="{{ $val }}" class="hidden checkbox-input"
                                {{ in_array($val, $currentProducts) ? 'checked' : '' }}>
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">{{ $label }}</span>
							</div>
						</label>
                        @endforeach
					</div>
					<div class="mt-3">
						<label class="custom-checkbox-card inline-block">
							<input type="checkbox" name="products[]" value="autres_produits" id="autres_produits_checkbox" class="hidden checkbox-input"
                                {{ $productOtherChecked ? 'checked' : '' }}>
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Autres:</span>
							</div>
						</label>
						<input type="text" name="products_autres" id="products_autres" 
							class="ml-3 px-4 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all inline-block w-auto min-w-[200px]"
							placeholder="Précisez..."
                            value="{{ $productOtherValue }}">
					</div>
				</div>

				<!-- Style ou inspiration recherchée -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Style ou inspiration recherchée</label>
					<div class="grid grid-cols-2 gap-3 mb-3">
                        @foreach(['moderne', 'classique', 'contemporaine', 'rustique'] as $style)
						<label class="custom-checkbox-card">
							<input type="checkbox" name="style[]" value="{{ $style }}" class="hidden checkbox-input"
                                {{ in_array($style, $currentStyles) ? 'checked' : '' }}>
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">{{ ucfirst($style) }}</span>
							</div>
						</label>
                        @endforeach
					</div>
					<div class="mt-3">
						<label class="custom-checkbox-card inline-block">
							<input type="checkbox" name="style[]" value="autres_style" id="autres_style_checkbox" class="hidden checkbox-input"
                                {{ $styleOtherChecked ? 'checked' : '' }}>
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Autres:</span>
							</div>
						</label>
						<input type="text" name="style_autres" id="style_autres" 
							class="ml-3 px-4 py-2 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all inline-block w-auto min-w-[200px]"
							placeholder="Précisez..."
                            value="{{ $styleOtherValue }}">
					</div>
				</div>

				<!-- Source du contact -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Source du contact</label>
					<div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php $source = old('source', $client->source); @endphp
						<label class="custom-chip-option">
							<input type="radio" name="source" value="reseaux_sociaux" class="hidden chip-input" {{ $source == 'reseaux_sociaux' ? 'checked' : '' }}>
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
								<span class="text-sm">Réseaux sociaux</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="publicite" class="hidden chip-input" {{ $source == 'publicite' ? 'checked' : '' }}>
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
								<span class="text-sm">Publicité</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="recommandation" class="hidden chip-input" {{ $source == 'recommandation' ? 'checked' : '' }}>
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
								<span class="text-sm">Recommandation</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="passage_showroom" class="hidden chip-input" {{ $source == 'passage_showroom' ? 'checked' : '' }}>
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
								<span class="text-sm">Passage showroom</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="autre" class="hidden chip-input" {{ $source == 'autre' ? 'checked' : '' }}>
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
								<span class="text-sm">Autre</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Devis demandé -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Devis demandé ?</label>
					<div class="grid grid-cols-2 gap-4">
                        @php $devis = old('devis_demande', $client->devis_demande); @endphp
						<label class="custom-toggle-card">
							<input type="radio" name="devis_demande" value="1" class="hidden toggle-input" {{ $devis ? 'checked' : '' }}>
							<div class="toggle-card-content">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
								</svg>
								<span class="font-medium">Oui</span>
							</div>
						</label>
						<label class="custom-toggle-card">
							<input type="radio" name="devis_demande" value="0" class="hidden toggle-input" {{ !$devis ? 'checked' : '' }}>
							<div class="toggle-card-content">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
								</svg>
								<span class="font-medium">Non</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Conseiller / Rep Assignment -->
				@auth
					@if(Auth::user()->isAdmin())
						<!-- Admin: Dropdown to assign rep -->
						<div class="form-field">
							<label class="block text-sm font-medium text-neutral-700 mb-2">Assigner à un conseiller *</label>
							<select name="assigned_rep_id" id="assigned_rep_id" required
								class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all">
								<option value="">Sélectionner un conseiller</option>
								@foreach($reps as $rep)
									<option value="{{ $rep->id }}" {{ old('assigned_rep_id', $client->user_id) == $rep->id ? 'selected' : '' }}>
                                        {{ $rep->name }}
                                    </option>
								@endforeach
							</select>
							<p class="text-xs text-neutral-400 mt-1">Le client sera assigné au conseiller sélectionné</p>
						</div>
					@else
						<!-- Rep: Hidden field (auto-assigned) -->
                        <div class="form-field">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Conseiller</label>
                            <input type="text" value="{{ $client->conseiller ?? Auth::user()->name }}" disabled
                                class="w-full px-4 py-3 border border-neutral-200 bg-neutral-50 text-neutral-500 rounded-xl cursor-not-allowed">
						    <input type="hidden" name="conseiller" value="{{ $client->conseiller ?? Auth::user()->name }}">
                        </div>
					@endif
				@endauth

				<!-- Notes -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Remarques <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<textarea name="notes" id="notes" rows="3"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ajoutez des observations ou besoins spécifiques...">{{ old('notes', $client->notes) }}</textarea>
				</div>

				<!-- Submit Button -->
				<div class="flex justify-between items-center pt-4">
                    <a href="{{ route('clients.index') }}" class="text-sm text-neutral-500 hover:text-black underline">Annuler</a>
					<button type="submit"
						class="px-8 py-3 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all font-medium shadow-sm hover:shadow-md">
						✓ Mettre à jour
					</button>
				</div>
			</div>
		</form>
	</div>

	<style>
		/* Custom Radio Cards */
		.custom-radio-card {
			cursor: pointer;
		}

		.radio-card-content {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			padding: 1.5rem;
			border: 2px solid #e5e5e5;
			border-radius: 1rem;
			background: white;
			transition: all 0.3s ease;
			min-height: 120px;
		}

		.radio-card-content svg {
			color: #737373;
			transition: all 0.3s ease;
		}

		.radio-card-content span {
			color: #525252;
			transition: all 0.3s ease;
		}

		.custom-radio-card:hover .radio-card-content {
			border-color: #000;
			background: #fafafa;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
		}

		.radio-input:checked + .radio-card-content {
			border-color: #000;
			background: #E6AF5D;
			box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
		}

		.radio-input:checked + .radio-card-content svg,
		.radio-input:checked + .radio-card-content span {
			color: white;
		}

		/* Custom Chip Options */
		.custom-chip-option {
			cursor: pointer;
		}

		.chip-content {
			display: flex;
			align-items: center;
			gap: 0.5rem;
			padding: 0.75rem 1rem;
			border: 2px solid #e5e5e5;
			border-radius: 9999px;
			background: white;
			transition: all 0.3s ease;
		}

		.chip-content svg {
			color: #737373;
			transition: all 0.3s ease;
			flex-shrink: 0;
		}

		.chip-content span {
			color: #525252;
			transition: all 0.3s ease;
			font-weight: 500;
		}

		.custom-chip-option:hover .chip-content {
			border-color: #a3a3a3;
			background: #fafafa;
			transform: translateY(-1px);
		}

		.chip-input:checked + .chip-content {
			border-color: #E6AF5F;
			background: #E6AF5D;
		}

		.chip-input:checked + .chip-content svg,
		.chip-input:checked + .chip-content span {
			color: white;
		}

		/* Custom Checkbox Cards */
		.custom-checkbox-card {
			cursor: pointer;
		}

		.checkbox-card-content {
			display: flex;
			align-items: center;
			gap: 0.75rem;
			padding: 0.875rem 1rem;
			border: 2px solid #e5e5e5;
			border-radius: 0.75rem;
			background: white;
			transition: all 0.3s ease;
			position: relative;
		}

		.checkbox-icon {
			width: 24px;
			height: 24px;
			border: 2px solid #d4d4d4;
			border-radius: 0.375rem;
			display: flex;
			align-items: center;
			justify-content: center;
			background: white;
			transition: all 0.3s ease;
			flex-shrink: 0;
		}

		.checkbox-icon svg {
			opacity: 0;
			transform: scale(0);
			transition: all 0.3s ease;
			color: white;
		}

		.checkbox-card-content span {
			color: #525252;
			transition: all 0.3s ease;
		}

		.custom-checkbox-card:hover .checkbox-card-content {
			border-color: #a3a3a3;
			background: #fafafa;
		}

		.checkbox-input:checked + .checkbox-card-content {
			border-color: #000;
			background: #fafafa;
		}

		.checkbox-input:checked + .checkbox-card-content .checkbox-icon {
			background: #E6AF5D;
			border-color: #E6AF5D;
		}

		.checkbox-input:checked + .checkbox-card-content .checkbox-icon svg {
			opacity: 1;
			transform: scale(1);
		}

		.checkbox-input:checked + .checkbox-card-content span {
			color: #000;
			font-weight: 600;
		}

		/* Custom Toggle Cards */
		.custom-toggle-card {
			cursor: pointer;
		}

		.toggle-card-content {
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			gap: 0.5rem;
			padding: 1.25rem;
			border: 2px solid #e5e5e5;
			border-radius: 0.75rem;
			background: white;
			transition: all 0.3s ease;
		}

		.toggle-card-content svg {
			color: #737373;
			transition: all 0.3s ease;
		}

		.toggle-card-content span {
			color: #525252;
			transition: all 0.3s ease;
		}

		.custom-toggle-card:hover .toggle-card-content {
			border-color: #a3a3a3;
			background: #fafafa;
			transform: translateY(-2px);
		}

		.toggle-input:checked + .toggle-card-content {
			border-color: #000;
			background: #000;
		}

		.toggle-input:checked + .toggle-card-content svg,
		.toggle-input:checked + .toggle-card-content span {
			color: white;
		}

		.form-field {
			animation: fadeInUp 0.5s ease forwards;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
	</style>

	<script>
		// Show/hide company fields based on client type
		document.querySelectorAll('input[name="client_type"]').forEach(radio => {
			radio.addEventListener('change', function () {
				const companyFields = document.getElementById('company_fields');
				if (this.value === 'professionnel') {
					companyFields.style.display = 'block';
					companyFields.style.opacity = '0';
					setTimeout(() => {
						companyFields.style.transition = 'opacity 0.3s ease';
						companyFields.style.opacity = '1';
					}, 10);
				} else {
					companyFields.style.opacity = '0';
					setTimeout(() => {
						companyFields.style.display = 'none';
					}, 300);
				}
			});
		});

		// Handle "Autres" products text field
		const autresProduitsCheckbox = document.getElementById('autres_produits_checkbox');
		const productsAutresInput = document.getElementById('products_autres');
		
		if (autresProduitsCheckbox && productsAutresInput) {
            // Initial state check
            productsAutresInput.disabled = !autresProduitsCheckbox.checked;

            // Event listener
			autresProduitsCheckbox.addEventListener('change', function() {
				productsAutresInput.disabled = !this.checked;
				if (!this.checked) {
					productsAutresInput.value = '';
				}
			});
		}

		// Handle "Autres" style text field
		const autresStyleCheckbox = document.getElementById('autres_style_checkbox');
		const styleAutresInput = document.getElementById('style_autres');
		
		if (autresStyleCheckbox && styleAutresInput) {
            // Initial state check
            styleAutresInput.disabled = !autresStyleCheckbox.checked;

            // Event listener
			autresStyleCheckbox.addEventListener('change', function() {
				styleAutresInput.disabled = !this.checked;
				if (!this.checked) {
					styleAutresInput.value = '';
				}
			});
		}

		// Form validation and data processing
		document.getElementById('clientForm').addEventListener('submit', function(e) {
			const fullName = document.getElementById('full_name').value.trim();
			const phone = document.getElementById('phone').value.trim();
			const clientType = document.querySelector('input[name="client_type"]:checked');

			if (!fullName || !phone || !clientType) {
				e.preventDefault();
				alert('Veuillez remplir tous les champs obligatoires (Type de client, Nom complet, Téléphone)');
				return false;
			}

			// Process products: if "Autres" is checked and has text, add it to products array
			if (autresProduitsCheckbox && autresProduitsCheckbox.checked && productsAutresInput.value.trim()) {
				// Remove "autres_produits" from products array if it exists
				const productsInputs = document.querySelectorAll('input[name="products[]"]:checked');
				productsInputs.forEach(input => {
					if (input.value === 'autres_produits') {
						input.checked = false;
					}
				});
				// Create a hidden input with the custom product value
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'products[]';
				hiddenInput.value = 'Autres: ' + productsAutresInput.value.trim();
				this.appendChild(hiddenInput);
			}

			// Process style: if "Autres" is checked and has text, add it to style array
			if (autresStyleCheckbox && autresStyleCheckbox.checked && styleAutresInput.value.trim()) {
				// Remove "autres_style" from style array if it exists
				const styleInputs = document.querySelectorAll('input[name="style[]"]:checked');
				styleInputs.forEach(input => {
					if (input.value === 'autres_style') {
						input.checked = false;
					}
				});
				// Create a hidden input with the custom style value
				const hiddenInput = document.createElement('input');
				hiddenInput.type = 'hidden';
				hiddenInput.name = 'style[]';
				hiddenInput.value = 'Autres: ' + styleAutresInput.value.trim();
				this.appendChild(hiddenInput);
			}
		});
	</script>

@endsection