@extends('layouts.dashboard')

@section('content')
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex items-center gap-4">
            <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Retour aux publications
            </a>
        </div>

        <div>
            <h1 class="text-3xl font-bold tracking-tight mb-2">Créer une nouvelle publication</h1>
            <p class="text-gray-500">Créez un nouveau événement ou formation</p>
        </div>

        <form action="{{ route('campaigns.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @php
                $hasActiveSubscription = \App\Models\Subscription::hasActiveSubscription(Auth::id(), \App\Models\SubscriptionPlan::TYPE_CREATE_ACTIVITIES);
            @endphp

            <div class="tabs">
                <!-- Onglets -->
                <div class="flex border-b mb-6">
                    <button type="button" class="tab-button active py-2 px-4 border-b-2 border-blue-600 font-medium" data-tab="details">
                        Détails de la publication
                    </button>
                    <button type="button" class="tab-button py-2 px-4 text-gray-500 hover:text-gray-700" data-tab="categories">
                        Catégories
                    </button>
                    <button type="button" class="tab-button py-2 px-4 text-gray-500 hover:text-gray-700" data-tab="preview">
                        Aperçu
                    </button>
                </div>

                <!-- Contenu des onglets -->
                <div id="details" class="tab-content active space-y-6">
                    <div class="grid gap-6">
                        <!-- Type de publication -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Type de publication</label>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input id="event" name="type" type="radio" value="event"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        {{ old('type', 'event') == 'event' ? 'checked' : '' }}>
                                    <label for="event" class="ml-2 block text-sm text-gray-700">Événement</label>
                                </div>
                                <div class="flex items-center">
                                    <input id="training" name="type" type="radio" value="training"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                        {{ old('type') == 'training' ? 'checked' : '' }}>
                                    <label for="training" class="ml-2 block text-sm text-gray-700">Formation</label>
                                </div>
                            </div>
                        </div>

                        <!-- Titre -->
                        <div class="space-y-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Titre</label>
                            <input type="text" id="title" name="title" placeholder="Entrez le titre"
                                   class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                   value="{{ old('title') }}">
                            @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="space-y-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                      placeholder="Entrez la description">{{ old('description') }}</textarea>
                        </div>

                        <!-- Fichier -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Fichier</label>
                            <div class="rounded-lg border border-gray-300 bg-gray-50 p-6 flex flex-col items-center justify-center gap-4">
                                <div id="file-preview" class="bg-gray-200 rounded-lg w-full h-40 flex flex-col items-center justify-center {{ old('file') ? 'hidden' : '' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
                                    </svg>
                                    <p class="text-sm text-gray-500">Aucun fichier sélectionné</p>
                                </div>
                                <input type="file" id="file" name="file" class="hidden">
                                <button type="button" onclick="document.getElementById('file').click()"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    Télécharger un fichier
                                </button>
                            </div>
                        </div>

                        <!-- Dates et lieu -->
                        <div class="grid gap-6">
                            <!-- Dates -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Date de début -->
                                <div class="space-y-2">
                                    <label for="start_date" class="block text-sm font-medium text-gray-700">Date de début</label>
                                    <input type="date" id="start_date" name="start_date"
                                           class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                           value="{{ old('start_date') }}">
                                </div>

                                <!-- Date de fin (visible pour les formations) -->
                                <div id="end-date-container" class="space-y-2 {{ old('type', 'event') == 'event' ? 'hidden' : '' }}">
                                    <label for="end_date" class="block text-sm font-medium text-gray-700">Date de fin</label>
                                    <input type="date" id="end_date" name="end_date"
                                           class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                           value="{{ old('end_date') }}">
                                </div>
                            </div>

                            <!-- Zone / Lieu -->
                            <div class="space-y-2">
                                <label for="zone" class="block text-sm font-medium text-gray-700">Zone (ville)</label>
                                <select id="zone" name="zone" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base bg-white">
                                    <option value="">— Choisir une ville —</option>
                                    @foreach($zones ?? [] as $zone)
                                        <option value="{{ $zone['name'] }}" {{ old('zone') === $zone['name'] ? 'selected' : '' }}>{{ $zone['name'] }} – {{ $zone['region'] }}</option>
                                    @endforeach
                                    <option value="other" {{ old('zone') === 'other' ? 'selected' : '' }}>Autre (précisez ci-dessous)</option>
                                </select>
                            </div>
                            <div id="location-other-wrap" class="space-y-2 {{ old('zone') === 'other' ? '' : 'hidden' }}">
                                <label for="location" class="block text-sm font-medium text-gray-700">Lieu (si autre)</label>
                                <input type="text" id="location" name="location" placeholder="Ville ou adresse"
                                       class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                       value="{{ old('location') }}">
                            </div>
                        </div>

                        @if(!empty($tarifsDiffusion))
                        <div class="rounded-lg border border-blue-100 bg-blue-50/50 p-4">
                            <p class="text-sm font-medium text-blue-900 mb-2">Tarifs de diffusion</p>
                            <ul class="text-sm text-blue-800 space-y-1">
                                @foreach($tarifsDiffusion as $t)
                                    <li>{{ $t['label'] }} : {{ number_format($t['amount'], 0, ',', ' ') }} FCFA</li>
                                @endforeach
                            </ul>
                            <p class="text-xs text-blue-700 mt-2">Les formations et événements gratuits peuvent être publiés sans frais.</p>
                        </div>
                        @endif

                        <!-- Montant et paiement -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label for="amount" class="block text-sm font-medium text-gray-700">Montant</label>
                                <input type="number" id="amount" name="amount" placeholder="Entrez le montant"
                                       class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                       value="{{ old('amount') }}">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Paiement</label>
                                <div class="flex items-center">
                                    <input id="canPaid" name="canPaid" type="checkbox" value="1"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        {{ old('canPaid') ? 'checked' : '' }}>
                                    <label for="canPaid" class="ml-2 block text-sm text-gray-700">Paiement possible</label>
                                </div>
                            </div>
                        </div>

                        <!-- Lien -->
                        <div class="space-y-2">
                            <label for="link" class="block text-sm font-medium text-gray-700">Lien</label>
                            <input type="url" id="link" name="link" placeholder="Entrez un lien URL"
                                   class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                   value="{{ old('link') }}">
                        </div>

                        <!-- Visibilité et statut -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Visibilité</label>
                                <div class="flex items-center">
                                    <input id="isPrivate" name="isPrivate" type="checkbox" value="1"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                        {{ old('isPrivate') ? 'checked' : '' }}>
                                    <label for="isPrivate" class="ml-2 block text-sm text-gray-700">Privé</label>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <button type="submit" name="status" value="brouillon" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Enregistrer comme brouillon
                        </button>
                        <button type="button" onclick="showTab('categories')" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Continuer vers les catégories
                        </button>
                    </div>
                </div>

                <!-- Onglet Catégories -->
                <div id="categories" class="tab-content hidden space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Catégories</h2>
                            <p class="text-gray-500">
                                Sélectionnez des catégories existantes ou créez-en de nouvelles pour classer votre publication.
                            </p>
                        </div>

                        <!-- Catégories existantes -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Catégories existantes</h3>
                            @if($categories->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($categories as $category)
                                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mr-3">
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                                <span class="block text-xs text-gray-500">{{ ucfirst($category->type) }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 text-sm">Aucune catégorie existante. Créez-en de nouvelles ci-dessous.</p>
                            @endif
                        </div>

                        <!-- Créer de nouvelles catégories -->
                        <div class="space-y-4 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Créer de nouvelles catégories</h3>
                            <div class="space-y-2">
                                <label for="new_categories" class="block text-sm font-medium text-gray-700">Nouvelles catégories</label>
                                <input type="text" id="new_categories" name="new_categories" 
                                       placeholder="Entrez les noms des catégories séparés par des virgules (ex: Marketing, Formation, Événement)"
                                       class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                                <p class="text-xs text-gray-500">Séparez plusieurs catégories par des virgules. Elles seront automatiquement créées lors de la publication.</p>
                            </div>
                        </div>

                        <!-- Liste des catégories sélectionnées -->
                        <div class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700">Catégories sélectionnées</h4>
                            <div id="selected-categories" class="flex flex-wrap gap-2">
                                <!-- Les catégories sélectionnées apparaîtront ici -->
                            </div>
                        </div>

                        <div class="flex justify-end gap-4">
                            <button type="button" onclick="showTab('details')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Retour aux détails
                            </button>
                            <button type="button" onclick="showTab('preview')" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Continuer vers l'aperçu
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Onglet Aperçu -->
                <div id="preview" class="tab-content hidden space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xl font-semibold mb-2">Aperçu de la publication</h2>
                            <p class="text-gray-500">Vérifiez votre publication avant enregistrement.</p>
                        </div>

                        <div class="rounded-lg border border-gray-300 bg-white shadow-sm">
                            <div class="p-6 space-y-6">
                                <!-- Fichier/Image -->
                                <div id="preview-file" class="bg-gray-200 rounded-lg w-full h-60 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                        <polyline points="13 2 13 9 20 9"></polyline>
                                    </svg>
                                </div>

                                <!-- Titre et infos -->
                                <div>
                                    <h3 id="preview-title" class="text-2xl font-bold">Titre de la publication</h3>
                                    <div id="preview-meta" class="flex items-center gap-2 text-gray-500 mt-1">
                                        <span id="preview-type">Type</span>
                                        <span>•</span>
                                        <span id="preview-dates">Dates</span>
                                        <span id="preview-location-container" class="hidden">
                                            <span>•</span>
                                            <span id="preview-location">Lieu</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <h4 class="font-medium mb-2">Description</h4>
                                    <p id="preview-description" class="text-gray-500">
                                        Description de la publication apparaîtra ici.
                                    </p>
                                </div>

                                <!-- Détails supplémentaires -->
                                <div id="preview-additional-details" class="space-y-4">
                                    <!-- Rempli dynamiquement -->
                                </div>

                                <!-- Catégories -->
                                <div>
                                    <h4 class="font-medium mb-2">Catégories</h4>
                                    <div id="preview-categories" class="flex flex-wrap gap-2">
                                        <!-- Rempli dynamiquement -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                            <strong>Activité gratuite :</strong> si le montant est à 0 et que « Paiement possible » est décoché (formation), vous pouvez <strong>publier sans abonnement</strong>. L’abonnement « Création d’activités » n’est requis que pour les activités payantes.
                        </p>
                        <div class="flex justify-end gap-4">
                            <button type="button" onclick="showTab('categories')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Retour aux catégories
                            </button>
                            @if(!$hasActiveSubscription)
                                <a href="{{ route('subscriptions.checkout', ['plan' => 'create_activities']) }}" class="inline-flex items-center rounded-md border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 shadow-sm hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    S’abonner pour publier des activités payantes
                                </a>
                            @endif
                            <button type="submit" name="status" value="publiée"
                                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Publier
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Gestion des onglets
        function showTab(tabId) {
            // Masquer tous les onglets
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('hidden');
            });

            // Désactiver tous les boutons d'onglet
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-600', 'text-gray-900');
                button.classList.add('text-gray-500');
            });

            // Afficher l'onglet sélectionné
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('active');

            // Activer le bouton de l'onglet
            document.querySelector(`.tab-button[data-tab="${tabId}"]`).classList.add('active', 'border-blue-600', 'text-gray-900');
            document.querySelector(`.tab-button[data-tab="${tabId}"]`).classList.remove('text-gray-500');

            // Mettre à jour l'aperçu si on est sur l'onglet preview
            if (tabId === 'preview') {
                updatePreview();
            }
        }

        // Mise à jour de l'aperçu
        function updatePreview() {
            // Titre
            const title = document.getElementById('title').value || 'Titre de la publication';
            document.getElementById('preview-title').textContent = title;

            // Type
            const type = document.querySelector('input[name="type"]:checked').value === 'event' ? 'Événement' : 'Formation';
            document.getElementById('preview-type').textContent = type;

            // Dates
            const startDate = document.getElementById('start_date').value ? new Date(document.getElementById('start_date').value).toLocaleDateString() : 'Non spécifié';

            if (document.querySelector('input[name="type"]:checked').value === 'training') {
                const endDate = document.getElementById('end_date').value ? new Date(document.getElementById('end_date').value).toLocaleDateString() : 'Non spécifié';
                document.getElementById('preview-dates').textContent = `${startDate} - ${endDate}`;

                // Afficher le lieu pour les formations
                const zoneSelect = document.getElementById('zone');
                const locationInput = document.getElementById('location');
                const location = (zoneSelect && zoneSelect.value === 'other' && locationInput) ? locationInput.value : (zoneSelect ? zoneSelect.selectedOptions[0]?.text : '') || (locationInput ? locationInput.value : '') || 'Non spécifié';
                document.getElementById('preview-location').textContent = location;
                document.getElementById('preview-location-container').classList.remove('hidden');
            } else {
                document.getElementById('preview-dates').textContent = startDate;
                document.getElementById('preview-location-container').classList.add('hidden');
            }

            // Description
            const description = document.getElementById('description').value || 'Description de la publication apparaîtra ici.';
            document.getElementById('preview-description').textContent = description;

            // Fichier
            const fileInput = document.getElementById('file');
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewFile = document.getElementById('preview-file');
                    previewFile.innerHTML = `<img src="${e.target.result}" class="w-full h-60 object-cover rounded-lg">`;
                };
                reader.readAsDataURL(fileInput.files[0]);
            }

            // Détails supplémentaires
            const additionalDetails = document.getElementById('preview-additional-details');
            additionalDetails.innerHTML = '';

            // Montant
            const amount = document.getElementById('amount').value;
            if (amount) {
                const amountHtml = `
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Montant:</span>
                        <span class="text-sm text-gray-500">${amount} €</span>
                    </div>
                `;
                additionalDetails.insertAdjacentHTML('beforeend', amountHtml);
            }

            // Paiement
            const canPaid = document.getElementById('canPaid').checked;
            if (canPaid) {
                const paidHtml = `
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Paiement:</span>
                        <span class="text-sm text-gray-500">Accepté</span>
                    </div>
                `;
                additionalDetails.insertAdjacentHTML('beforeend', paidHtml);
            }

            // Lien
            const link = document.getElementById('link').value;
            if (link) {
                const linkHtml = `
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-700">Lien:</span>
                        <a href="${link}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800">${link}</a>
                    </div>
                `;
                additionalDetails.insertAdjacentHTML('beforeend', linkHtml);
            }

            // Visibilité
            const isPrivate = document.getElementById('isPrivate').checked;
            const visibilityHtml = `
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Visibilité:</span>
                    <span class="text-sm text-gray-500">${isPrivate ? 'Privé' : 'Public'}</span>
                </div>
            `;
            additionalDetails.insertAdjacentHTML('beforeend', visibilityHtml);



            // Mettre à jour les catégories dans l'aperçu
            updatePreviewCategories();
        }

        // Gestion du type de publication
        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('end-date-container').classList.toggle('hidden', this.value !== 'training');
                updatePreview();
            });
        });

        // Zone / lieu autre
        const zoneSelect = document.getElementById('zone');
        const locationOtherWrap = document.getElementById('location-other-wrap');
        if (zoneSelect && locationOtherWrap) {
            zoneSelect.addEventListener('change', function() {
                locationOtherWrap.classList.toggle('hidden', this.value !== 'other');
                updatePreview();
            });
        }

        // Gestion du fichier
        document.getElementById('file').addEventListener('change', function(e) {
            const previewDiv = document.getElementById('file-preview');

            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewDiv.classList.add('hidden');
                    // Pas besoin de prévisualisation ici car elle sera gérée dans l'aperçu
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Gestion des catégories
        function updateSelectedCategories() {
            const selectedContainer = document.getElementById('selected-categories');
            selectedContainer.innerHTML = '';

            // Catégories existantes sélectionnées
            document.querySelectorAll('input[name="categories[]"]:checked').forEach(checkbox => {
                const categoryName = checkbox.closest('label').querySelector('.text-gray-900').textContent;
                const categoryType = checkbox.closest('label').querySelector('.text-gray-500').textContent;
                
                const badgeHtml = `
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                        ${categoryName} (${categoryType})
                    </span>
                `;
                selectedContainer.insertAdjacentHTML('beforeend', badgeHtml);
            });

            // Nouvelles catégories
            const newCategoriesInput = document.getElementById('new_categories');
            if (newCategoriesInput.value.trim()) {
                const newCategories = newCategoriesInput.value.split(',').map(cat => cat.trim()).filter(cat => cat);
                newCategories.forEach(categoryName => {
                    const badgeHtml = `
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                            ${categoryName} (Nouveau)
                        </span>
                    `;
                    selectedContainer.insertAdjacentHTML('beforeend', badgeHtml);
                });
            }

            updatePreviewCategories();
        }

        // Mise à jour des catégories dans l'aperçu
        function updatePreviewCategories() {
            const previewContainer = document.getElementById('preview-categories');
            previewContainer.innerHTML = '';

            // Catégories existantes sélectionnées
            document.querySelectorAll('input[name="categories[]"]:checked').forEach(checkbox => {
                const categoryName = checkbox.closest('label').querySelector('.text-gray-900').textContent;
                
                const badgeHtml = `
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                        ${categoryName}
                    </span>
                `;
                previewContainer.insertAdjacentHTML('beforeend', badgeHtml);
            });

            // Nouvelles catégories
            const newCategoriesInput = document.getElementById('new_categories');
            if (newCategoriesInput.value.trim()) {
                const newCategories = newCategoriesInput.value.split(',').map(cat => cat.trim()).filter(cat => cat);
                newCategories.forEach(categoryName => {
                    const badgeHtml = `
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                            ${categoryName}
                        </span>
                    `;
                    previewContainer.insertAdjacentHTML('beforeend', badgeHtml);
                });
            }
        }

        // Écouteurs pour les catégories
        document.querySelectorAll('input[name="categories[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCategories);
        });

        document.getElementById('new_categories').addEventListener('input', updateSelectedCategories);

        // Initialisation des boutons d'onglet
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                showTab(this.dataset.tab);
            });
        });

        // Écouteurs pour mettre à jour l'aperçu en temps réel
        ['title', 'description', 'start_date', 'end_date', 'location', 'amount', 'link', 'status'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updatePreview);
                element.addEventListener('change', updatePreview);
            }
        });

        document.getElementById('canPaid').addEventListener('change', updatePreview);
        document.getElementById('isPrivate').addEventListener('change', updatePreview);
    </script>
@endsection
