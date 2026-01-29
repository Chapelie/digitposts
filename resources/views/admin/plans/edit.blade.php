@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Modifier le plan</h1>
            <p class="text-gray-500">{{ $plan->name }} ({{ $plan->type }})</p>
        </div>
        <a href="{{ route('admin.plans.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            ← Retour aux plans
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <p class="text-sm text-gray-500">Type du plan : <strong>{{ $plan->type }}</strong> (non modifiable)</p>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                <input name="name" type="text" value="{{ old('name', $plan->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('description', $plan->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant (XOF)</label>
                <input name="amount" type="number" min="0" step="1" value="{{ old('amount', (int) $plan->amount) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durée (semaines)</label>
                <input name="duration_weeks" type="number" min="1" max="52" value="{{ old('duration_weeks', $plan->duration_weeks) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                @error('duration_weeks') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="flex items-center gap-2">
                    <input name="is_active" type="checkbox" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                    <span class="text-sm font-medium text-gray-700">Plan actif (proposé aux utilisateurs)</span>
                </label>
                @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2 flex gap-3">
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                    Enregistrer
                </button>
                <a href="{{ route('admin.plans.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
