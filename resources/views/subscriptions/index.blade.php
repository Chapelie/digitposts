@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight mb-2">Mes Abonnements</h1>
        <p class="text-gray-500">Deux types d'abonnement : accès aux événements gratuits et droit de créer des activités</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-800">{{ session('info') }}</div>
    @endif

    <!-- Les deux plans -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
            $freePlan = $plans->firstWhere('type', \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS);
            $createPlan = $plans->firstWhere('type', \App\Models\SubscriptionPlan::TYPE_CREATE_ACTIVITIES);
        @endphp

        @if($freePlan)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-orange-500 px-6 py-3">
                    <h2 class="text-lg font-bold text-white">Accès événements gratuits</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4">{{ $freePlan->description }}</p>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-2xl font-bold text-gray-900">{{ number_format((float) $freePlan->amount, 0, ',', ' ') }} XOF</span>
                        <span class="text-gray-500">/ {{ $freePlan->duration_weeks }} semaines</span>
                    </div>
                    @if($activeFreeEvents)
                        <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-3">
                            <p class="text-green-800 font-medium">Actif jusqu'au {{ $activeFreeEvents->end_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-green-700">{{ $activeFreeEvents->end_date->diffInDays(now()) }} jours restants</p>
                        </div>
                        <div class="mt-3">
                            @include('partials.social-share', [
                                'url' => route('subscriptions.checkout', ['plan' => 'free_events']),
                                'title' => $freePlan->name . ' - ' . number_format($freePlan->amount, 0, ',', ' ') . ' XOF pour ' . $freePlan->duration_weeks . ' semaines',
                                'variant' => 'light'
                            ])
                        </div>
                    @else
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('subscriptions.checkout', ['plan' => 'free_events']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                                S'abonner
                            </a>
                            <div>
                                @include('partials.social-share', [
                                    'url' => route('subscriptions.checkout', ['plan' => 'free_events']),
                                    'title' => $freePlan->name . ' - ' . number_format($freePlan->amount, 0, ',', ' ') . ' XOF pour ' . $freePlan->duration_weeks . ' semaines',
                                    'variant' => 'light'
                                ])
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($createPlan)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-blue-600 px-6 py-3">
                    <h2 class="text-lg font-bold text-white">Création d'activités</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 text-sm mb-4">{{ $createPlan->description }}</p>
                    <div class="flex items-baseline gap-2 mb-4">
                        <span class="text-2xl font-bold text-gray-900">{{ number_format((float) $createPlan->amount, 0, ',', ' ') }} XOF</span>
                        <span class="text-gray-500">/ {{ $createPlan->duration_weeks }} semaines</span>
                    </div>
                    @if($activeCreateActivities)
                        <div class="rounded-lg bg-green-50 border border-green-200 p-4 mb-3">
                            <p class="text-green-800 font-medium">Actif jusqu'au {{ $activeCreateActivities->end_date->format('d/m/Y') }}</p>
                            <p class="text-sm text-green-700">{{ $activeCreateActivities->end_date->diffInDays(now()) }} jours restants</p>
                        </div>
                        <div class="mt-3">
                            @include('partials.social-share', [
                                'url' => route('subscriptions.checkout', ['plan' => 'create_activities']),
                                'title' => $createPlan->name . ' - ' . number_format($createPlan->amount, 0, ',', ' ') . ' XOF pour ' . $createPlan->duration_weeks . ' semaines',
                                'variant' => 'light'
                            ])
                        </div>
                    @else
                        <div class="flex flex-col gap-3">
                            <a href="{{ route('subscriptions.checkout', ['plan' => 'create_activities']) }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                S'abonner
                            </a>
                            <div>
                                @include('partials.social-share', [
                                    'url' => route('subscriptions.checkout', ['plan' => 'create_activities']),
                                    'title' => $createPlan->name . ' - ' . number_format($createPlan->amount, 0, ',', ' ') . ' XOF pour ' . $createPlan->duration_weeks . ' semaines',
                                    'variant' => 'light'
                                ])
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    @if($plans->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            Aucun plan d'abonnement disponible.
        </div>
    @endif

    <!-- Historique -->
    @if($subscriptions->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des abonnements</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paiement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($subscriptions as $s)
                            <tr>
                                <td class="px-6 py-4 text-sm">
                                    @if($s->plan_type === \App\Models\SubscriptionPlan::TYPE_FREE_EVENTS)
                                        <span class="text-orange-700">Gratuit</span>
                                    @else
                                        <span class="text-blue-700">Création</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $s->start_date->format('d/m/Y') }} → {{ $s->end_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm">{{ number_format((float) $s->amount, 0, ',', ' ') }} XOF</td>
                                <td class="px-6 py-4 text-sm">
                                    @if($s->status === 'active' && $s->end_date->isFuture())
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                    @elseif($s->end_date->isPast())
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Expiré</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $s->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $s->payment_date ? $s->payment_date->format('d/m/Y H:i') : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $subscriptions->links() }}</div>
        </div>
    @endif
</div>
@endsection
