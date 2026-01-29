@extends('layouts.dashboard')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Plans d'abonnement</h1>
            <p class="text-gray-500">Modifier les montants et durées des deux types d'abonnement</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
            Voir les abonnements
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($plans as $plan)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                @if($plan->type === 'free_events') bg-orange-100 text-orange-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $plan->type === 'free_events' ? 'Gratuit (événements)' : 'Création d\'activités' }}
                            </span>
                            <h2 class="mt-2 text-lg font-semibold text-gray-900">{{ $plan->name }}</h2>
                            @if($plan->description)
                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $plan->description }}</p>
                            @endif
                        </div>
                        @if($plan->is_active)
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                        @else
                            <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactif</span>
                        @endif
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Montant</dt>
                            <dd class="font-semibold text-gray-900">{{ number_format((float) $plan->amount, 0, ',', ' ') }} XOF</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Durée</dt>
                            <dd class="font-semibold text-gray-900">{{ $plan->duration_weeks }} semaine(s)</dd>
                        </div>
                    </dl>
                    <div class="mt-4">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                            Modifier montant et durée
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center text-gray-500">
            Aucun plan d'abonnement. Exécutez <code class="bg-gray-100 px-2 py-1 rounded">php artisan db:seed --class=SubscriptionPlanSeeder</code>.
        </div>
    @endif
</div>
@endsection
