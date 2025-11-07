@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Base de données clients')
@section('page-description', 'Ajoutez et gérez vos clients pour mieux comprendre leurs préférences et sources')

@section('content')

	<!-- Simplified Client Form -->
	<div class="bg-white p-8 rounded-2xl shadow-sm border border-neutral-200 mb-10">
		<form action="{{ route('clients.store') }}" method="POST" id="clientForm">
			@csrf

			<div class="max-w-3xl mx-auto space-y-8">
				
				<!-- Type de client -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Type de client *</label>
					<div class="grid grid-cols-2 gap-4">
						<label class="custom-radio-card">
							<input type="radio" name="client_type" value="particulier" required class="hidden radio-input">
							<div class="radio-card-content">
								<svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
								</svg>
								<span class="font-medium">Particulier</span>
							</div>
						</label>
						<label class="custom-radio-card">
							<input type="radio" name="client_type" value="professionnel" class="hidden radio-input">
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
						placeholder="Ex: Ahmed Benali">
				</div>

				<!-- Company fields (hidden by default) -->
				<div id="company_fields" style="display: none;" class="space-y-6">
					<div class="form-field">
						<label class="block text-sm font-medium text-neutral-700 mb-2">Nom de l'entreprise</label>
						<input type="text" name="company_name" id="company_name"
							class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
							placeholder="Ex: Deco Design SARL">
					</div>
				</div>

				<!-- Téléphone -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Téléphone *</label>
					<input type="tel" name="phone" id="phone" required
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: 0612345678">
				</div>

				<!-- Email (optional) -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Email <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<input type="email" name="email" id="email"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: client@email.com">
				</div>

				<!-- Ville -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Ville <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<input type="text" name="city" id="city"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: Casablanca">
				</div>

				<hr class="border-neutral-200">

				<!-- Produits d'intérêt - SIMPLIFIED -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Produits d'intérêt</label>
					<div class="grid grid-cols-2 gap-3">
						<label class="custom-checkbox-card">
							<input type="checkbox" name="products[]" value="carrelage" class="hidden checkbox-input">
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Carrelage</span>
							</div>
						</label>
						<label class="custom-checkbox-card">
							<input type="checkbox" name="products[]" value="meubles" class="hidden checkbox-input">
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Meubles</span>
							</div>
						</label>
						<label class="custom-checkbox-card">
							<input type="checkbox" name="products[]" value="sanitaires" class="hidden checkbox-input">
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Sanitaires</span>
							</div>
						</label>
						<label class="custom-checkbox-card">
							<input type="checkbox" name="products[]" value="autre" class="hidden checkbox-input">
							<div class="checkbox-card-content">
								<div class="checkbox-icon">
									<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
									</svg>
								</div>
								<span class="text-sm font-medium">Autre</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Source du contact -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Source du contact</label>
					<div class="grid grid-cols-2 md:grid-cols-3 gap-3">
						<label class="custom-chip-option">
							<input type="radio" name="source" value="reseaux_sociaux" class="hidden chip-input">
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
								</svg>
								<span class="text-sm">Réseaux sociaux</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="publicite" class="hidden chip-input">
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
								</svg>
								<span class="text-sm">Publicité</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="recommandation" class="hidden chip-input">
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
								</svg>
								<span class="text-sm">Recommandation</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="passage_showroom" class="hidden chip-input">
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
								</svg>
								<span class="text-sm">Passage showroom</span>
							</div>
						</label>
						<label class="custom-chip-option">
							<input type="radio" name="source" value="autre" class="hidden chip-input">
							<div class="chip-content">
								<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
								</svg>
								<span class="text-sm">Autre</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Devis demandé -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-3">Devis demandé ?</label>
					<div class="grid grid-cols-2 gap-4">
						<label class="custom-toggle-card">
							<input type="radio" name="devis_demande" value="1" class="hidden toggle-input">
							<div class="toggle-card-content">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
								</svg>
								<span class="font-medium">Oui</span>
							</div>
						</label>
						<label class="custom-toggle-card">
							<input type="radio" name="devis_demande" value="0" class="hidden toggle-input" checked>
							<div class="toggle-card-content">
								<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
								</svg>
								<span class="font-medium">Non</span>
							</div>
						</label>
					</div>
				</div>

				<!-- Conseiller -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Conseiller <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<input type="text" name="conseiller" id="conseiller"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ex: Mohammed Alami">
				</div>

				<!-- Notes -->
				<div class="form-field">
					<label class="block text-sm font-medium text-neutral-700 mb-2">Remarques <span class="text-neutral-400 text-xs">(optionnel)</span></label>
					<textarea name="notes" id="notes" rows="3"
						class="w-full px-4 py-3 border border-neutral-300 rounded-xl focus:ring-2 focus:ring-black focus:outline-none transition-all"
						placeholder="Ajoutez des observations ou besoins spécifiques..."></textarea>
				</div>

				<!-- Submit Button -->
				<div class="flex justify-end pt-4">
					<button type="submit"
						class="px-8 py-3 bg-black text-white rounded-xl hover:bg-neutral-800 transition-all font-medium shadow-sm hover:shadow-md">
						✓ Ajouter le client
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

		// Form validation
		document.getElementById('clientForm').addEventListener('submit', function(e) {
			const fullName = document.getElementById('full_name').value.trim();
			const phone = document.getElementById('phone').value.trim();
			const clientType = document.querySelector('input[name="client_type"]:checked');

			if (!fullName || !phone || !clientType) {
				e.preventDefault();
				alert('Veuillez remplir tous les champs obligatoires (Type de client, Nom complet, Téléphone)');
				return false;
			}
		});
	</script>

@endsection


