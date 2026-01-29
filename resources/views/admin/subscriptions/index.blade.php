@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Abonnements</h1>
            <p class="text-gray-500">Gestion des abonnements (CRUD)</p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
            Créer un abonnement
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input name="q" value="{{ $q }}" placeholder="Email, nom, téléphone, transaction, id…"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Tous</option>
                    <option value="active" @selected($status==='active')>active</option>
                    <option value="expired" @selected($status==='expired')>expired</option>
                    <option value="cancelled" @selected($status==='cancelled')>cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Paiement</label>
                <select name="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="">Tous</option>
                    <option value="pending" @selected($paymentStatus==='pending')>pending</option>
                    <option value="paid" @selected($paymentStatus==='paid')>paid</option>
                    <option value="failed" @selected($paymentStatus==='failed')>failed</option>
                    <option value="cancelled" @selected($paymentStatus==='cancelled')>cancelled</option>
                </select>
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white">Filtrer</button>
                <a href="{{ route('admin.subscriptions.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Réinitialiser</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $sub)
                    <tr>
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $sub->user->firstname ?? '' }} {{ $sub->user->lastname ?? '' }}</div>
                            <div class="text-gray-500">{{ $sub->user->email ?? '—' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            @if(($sub->plan_type ?? '') === 'free_events')
                                <span class="text-orange-700">Gratuit</span>
                            @else
                                <span class="text-blue-700">Création</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">
                            {{ optional($sub->start_date)->format('d/m/Y') }} → {{ optional($sub->end_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ number_format((float)$sub->amount, 0, ',', ' ') }} XOF
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                @if($sub->payment_status==='paid') bg-green-100 text-green-800
                                @elseif($sub->payment_status==='pending') bg-yellow-100 text-yellow-800
                                @elseif($sub->payment_status==='failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $sub->payment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold
                                @if($sub->status==='active') bg-blue-100 text-blue-800
                                @elseif($sub->status==='expired') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-right whitespace-nowrap">
                            <a href="{{ route('admin.subscriptions.show', $sub) }}" class="text-blue-600 hover:underline mr-3">Voir</a>
                            <a href="{{ route('admin.subscriptions.edit', $sub) }}" class="text-gray-900 hover:underline mr-3">Éditer</a>
                            <form action="{{ route('admin.subscriptions.destroy', $sub) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cet abonnement ?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Aucun abonnement.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $subscriptions->links() }}
    </div>
</div>
@endsection

