@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Inscriptions</h1>
            <p class="text-gray-500">Toutes les inscriptions de la plateforme</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour au dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Participant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activité</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paiement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($registrations as $registration)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">
                                            {{ strtoupper(substr($registration->user->firstname ?? 'U', 0, 1)) }}{{ strtoupper(substr($registration->user->lastname ?? '', 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $registration->user->firstname ?? '' }} {{ $registration->user->lastname ?? '' }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $registration->user->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 max-w-xs truncate">
                                {{ $registration->feed->feedable->title ?? 'Activité supprimée' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($registration->feed && $registration->feed->feedable_type === 'App\Models\Training')
                                    Formation
                                @else
                                    Événement
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'En attente',
                                    'confirmed' => 'Confirmée',
                                    'cancelled' => 'Annulée',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$registration->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$registration->status] ?? $registration->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $paymentColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'free' => 'bg-blue-100 text-blue-800',
                                ];
                                $paymentLabels = [
                                    'pending' => 'En attente',
                                    'paid' => 'Payé',
                                    'failed' => 'Échoué',
                                    'free' => 'Gratuit',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $paymentColors[$registration->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $paymentLabels[$registration->payment_status] ?? $registration->payment_status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($registration->amount_paid && $registration->amount_paid > 0)
                                {{ number_format($registration->amount_paid, 0, ',', ' ') }} XOF
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $registration->created_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucune inscription trouvée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $registrations->links() }}
    </div>
</div>
@endsection
