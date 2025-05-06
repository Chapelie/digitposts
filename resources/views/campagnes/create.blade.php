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

                            <!-- Lieu -->
                            <div class="space-y-2">
                                <label for="location" class="block text-sm font-medium text-gray-700">Lieu</label>
                                <input type="text" id="location" name="location" placeholder="Entrez le lieu"
                                       class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base"
                                       value="{{ old('location') }}">
                            </div>
                        </div>

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
                        <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
                                Ajoutez des catégories pour classer votre publication.
                            </p>
                        </div>

                        <!-- Sélection de catégories -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Type de catégorie</label>
                            <select id="category-type" class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                                <option value="">Sélectionnez un type</option>
                                <option value="theme">Thème</option>
                                <option value="audience">Public cible</option>
                                <option value="skill">Compétence</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label for="category-name" class="block text-sm font-medium text-gray-700">Nom de la catégorie</label>
                            <div class="flex gap-2">
                                <input type="text" id="category-name" placeholder="Entrez le nom de la catégorie"
                                       class="block w-full px-4 py-2 rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                                <button type="button" id="add-category-btn" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Ajouter
                                </button>
                            </div>
                        </div>

                        <!-- Liste des catégories ajoutées -->
                        <div id="categories-container" class="space-y-2">
                            <!-- Les catégories seront ajoutées dynamiquement ici -->
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

                        <div class="flex justify-end gap-4">
                            <button type="button" onclick="showTab('categories')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Retour aux catégories
                            </button>
                            <button type="submit" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
                const location = document.getElementById('location').value || 'Non spécifié';
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
        let categoryCounter = 0;
        const addedCategories = new Set();

        document.getElementById('add-category-btn').addEventListener('click', function() {
            const categoryType = document.getElementById('category-type').value;
            const categoryName = document.getElementById('category-name').value.trim();

            if (!categoryType || !categoryName) {
                alert('Veuillez sélectionner un type et entrer un nom pour la catégorie');
                return;
            }

            const categoryKey = `${categoryType}:${categoryName}`;
            if (addedCategories.has(categoryKey)) {
                alert('Cette catégorie a déjà été ajoutée');
                return;
            }

            categoryCounter++;
            addedCategories.add(categoryKey);

            const categoryHtml = `
                <div class="category-item flex items-center justify-between bg-gray-50 p-3 rounded-md" data-id="category-${categoryCounter}">
                    <div>
                        <span class="text-sm font-medium text-gray-700">${categoryType}:</span>
                        <span class="text-sm text-gray-500 ml-1">${categoryName}</span>
                        <input type="hidden" name="categories[${categoryCounter}][type]" value="${categoryType}">
                        <input type="hidden" name="categories[${categoryCounter}][name]" value="${categoryName}">
                    </div>
                    <button type="button" class="remove-category text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.getElementById('categories-container').insertAdjacentHTML('beforeend', categoryHtml);
            document.getElementById('category-name').value = '';

            // Gestion de la suppression
            document.querySelector(`.category-item[data-id="category-${categoryCounter}"] .remove-category`).addEventListener('click', function() {
                const categoryItem = this.closest('.category-item');
                const categoryKey = `${categoryItem.querySelector('input[name$="[type]"]').value}:${categoryItem.querySelector('input[name$="[name]"]').value}`;
                addedCategories.delete(categoryKey);
                categoryItem.remove();
                updatePreviewCategories();
            });

            updatePreviewCategories();
        });

        // Mise à jour des catégories dans l'aperçu
        function updatePreviewCategories() {
            const previewContainer = document.getElementById('preview-categories');
            previewContainer.innerHTML = '';

            document.querySelectorAll('.category-item').forEach(item => {
                const type = item.querySelector('input[name$="[type]"]').value;
                const name = item.querySelector('input[name$="[name]"]').value;

                const badgeHtml = `
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                        ${type}: ${name}
                    </span>
                `;
                previewContainer.insertAdjacentHTML('beforeend', badgeHtml);
            });
        }

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
