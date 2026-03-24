@extends('layouts.dashboard')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Modifier la campagne</h1>
            <a href="{{ route('dashboard.campaigns') }}" class="text-sm text-blue-600 hover:text-blue-800">Retour</a>
        </div>

        <form action="{{ route('campaigns.update', $feed->id) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-xl p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                <input type="text" name="title" value="{{ old('title', $campaign->title) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="4" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('description', $campaign->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', optional($campaign->start_date)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                @if($feed->feedable_type === 'App\Models\Training')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', optional($campaign->end_date)->format('Y-m-d\\TH:i')) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                @endif
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                    <input type="number" step="0.01" min="0" name="amount" value="{{ old('amount', $campaign->amount) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="brouillon" @selected(old('status', $feed->status)==='brouillon')>Brouillon</option>
                        <option value="publiée" @selected(old('status', $feed->status)==='publiée')>Publiée</option>
                    </select>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zone / Localisation</label>
                    <input type="text" name="location" value="{{ old('location', $campaign->location) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="file" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            @if($feed->feedable_type === 'App\Models\Training')
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Places</label>
                        <input type="text" name="place" value="{{ old('place', $campaign->place) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lien</label>
                        <input type="url" name="link" value="{{ old('link', $campaign->link) }}" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="canPaid" value="1" @checked(old('canPaid', $campaign->canPaid)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Paiement possible</span>
                </label>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catégories</label>
                <div class="grid md:grid-cols-3 gap-2">
                    @foreach($categories as $category)
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                   @checked(in_array($category->id, old('categories', $selectedCategoryIds)))>
                            <span>{{ $category->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3">
                <a href="{{ route('dashboard.campaigns') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</a>
                <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    </div>
@endsection
