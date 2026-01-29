@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Créer un abonnement</h1>
            <p class="text-gray-500">Création manuelle (Admin)</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="text-sm text-gray-600 hover:text-gray-900">← Retour</a>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.subscriptions.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email utilisateur</label>
                <input name="user_email" type="email" value="{{ old('user_email') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                @error('user_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de plan</label>
                <select name="plan_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    @forelse($plans as $p)
                        <option value="{{ $p->type }}" @selected(old('plan_type') === $p->type)>{{ $p->name }} ({{ number_format((float) $p->amount, 0, ',', ' ') }} XOF / {{ $p->duration_weeks }} sem.)</option>
                    @empty
                        <option value="free_events">Accès événements gratuits</option>
                        <option value="create_activities">Création d'activités</option>
                    @endforelse
                </select>
                @error('plan_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input name="start_date" type="datetime-local" value="{{ old('start_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                <p class="text-xs text-gray-500 mt-1">Vide = maintenant</p>
                @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input name="end_date" type="datetime-local" value="{{ old('end_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                <p class="text-xs text-gray-500 mt-1">Vide = +3 semaines</p>
                @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant (XOF)</label>
                <input name="amount" type="number" min="0" value="{{ old('amount', 2000) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut paiement</label>
                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    @foreach(['pending','paid','failed','cancelled'] as $ps)
                        <option value="{{ $ps }}" @selected(old('payment_status','pending')===$ps)>{{ $ps }}</option>
                    @endforeach
                </select>
                @error('payment_status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut abonnement</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    @foreach(['active','expired','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status','active')===$st)>{{ $st }}</option>
                    @endforeach
                </select>
                @error('status') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                <input name="payment_transaction_id" value="{{ old('payment_transaction_id') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date paiement</label>
                <input name="payment_date" type="datetime-local" value="{{ old('payment_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Annuler</a>
                <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Créer</button>
            </div>
        </form>
    </div>
</div>
@endsection

