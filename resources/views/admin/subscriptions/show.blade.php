@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Détails abonnement</h1>
            <p class="text-gray-500">{{ $subscription->user->email ?? '' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="px-4 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700">Éditer</a>
            <a href="{{ route('admin.subscriptions.index') }}" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Retour</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Utilisateur</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->user->firstname ?? '' }} {{ $subscription->user->lastname ?? '' }}</dd>
                <dd class="text-sm text-gray-500">{{ $subscription->user->email ?? '' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Type</dt>
                <dd class="text-sm text-gray-900">{{ ($subscription->plan_type ?? '') === 'free_events' ? 'Gratuit (événements)' : 'Création d\'activités' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Période</dt>
                <dd class="text-sm text-gray-900">{{ optional($subscription->start_date)->format('d/m/Y H:i') }} → {{ optional($subscription->end_date)->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Montant</dt>
                <dd class="text-sm text-gray-900">{{ number_format((float)$subscription->amount, 0, ',', ' ') }} XOF</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Statut paiement</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->payment_status }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Statut abonnement</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->status }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Transaction</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->payment_transaction_id ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Date paiement</dt>
                <dd class="text-sm text-gray-900">{{ $subscription->payment_date ? $subscription->payment_date->format('d/m/Y H:i') : '—' }}</dd>
            </div>
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Payment URL</dt>
                <dd class="text-sm text-gray-900 break-all">{{ $subscription->payment_url ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    <form action="{{ route('admin.subscriptions.destroy', $subscription) }}" method="POST" onsubmit="return confirm('Supprimer cet abonnement ?');">
        @csrf
        @method('DELETE')
        <button class="px-4 py-2 rounded-md border border-red-200 text-red-700 hover:bg-red-50">Supprimer</button>
    </form>
</div>
@endsection

