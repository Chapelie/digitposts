@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Éditer abonnement</h1>
            <p class="text-gray-500">{{ $subscription->user->email ?? '' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Voir</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Retour</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf
            @method('PUT')

            <div class="md:col-span-2">
                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-900">ID:</span> {{ $subscription->id }}
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de plan</label>
                <select name="plan_type" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                    <option value="free_events" @selected(old('plan_type', $subscription->plan_type ?? 'free_events') === 'free_events')>Gratuit (événements)</option>
                    <option value="create_activities" @selected(old('plan_type', $subscription->plan_type ?? '') === 'create_activities')>Création d'activités</option>
                </select>
                @error('plan_type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input name="start_date" type="datetime-local"
                       value="{{ old('start_date', optional($subscription->start_date)->format('Y-m-d\\TH:i')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date fin</label>
                <input name="end_date" type="datetime-local"
                       value="{{ old('end_date', optional($subscription->end_date)->format('Y-m-d\\TH:i')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('end_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Montant (XOF)</label>
                <input name="amount" type="number" min="0" value="{{ old('amount', (int) $subscription->amount) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut paiement</label>
                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    @foreach(['pending','paid','failed','cancelled'] as $ps)
                        <option value="{{ $ps }}" @selected(old('payment_status', $subscription->payment_status)===$ps)>{{ $ps }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut abonnement</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    @foreach(['active','expired','cancelled'] as $st)
                        <option value="{{ $st }}" @selected(old('status', $subscription->status)===$st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                <input name="payment_transaction_id" value="{{ old('payment_transaction_id', $subscription->payment_transaction_id) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment URL</label>
                <input name="payment_url" value="{{ old('payment_url', $subscription->payment_url) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date paiement</label>
                <input name="payment_date" type="datetime-local"
                       value="{{ old('payment_date', optional($subscription->payment_date)->format('Y-m-d\\TH:i')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md" />
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <button class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection

