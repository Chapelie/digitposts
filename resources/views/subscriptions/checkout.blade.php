@extends('layouts.app')

@section('title', $plan->name ?? 'Abonnement')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $plan->name ?? 'Abonnement' }}</h1>
            <p class="text-lg text-gray-600">{{ $plan->description ?? 'Abonnement ' . $plan->duration_weeks . ' semaines' }}</p>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-red-800 font-medium">{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <!-- Détails de l'abonnement -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $plan->name ?? 'Abonnement' }}</h2>
                    <p class="text-gray-600 mb-4">{{ $plan->description ?? '' }}</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Durée :</span>
                            <span class="text-gray-600">{{ $plan->duration_weeks ?? 3 }} semaines</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Début :</span>
                            <span class="text-gray-600">{{ $subscription->start_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Fin :</span>
                            <span class="text-gray-600">{{ $subscription->end_date->format('d/m/Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Bénéficiaire :</span>
                            <span class="text-gray-600">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé du paiement -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé de votre abonnement</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Abonnement</span>
                    <span class="font-medium">{{ $plan->name ?? 'Abonnement' }} ({{ $plan->duration_weeks ?? 3 }} sem.)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Utilisateur</span>
                    <span class="font-medium">{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium">{{ Auth::user()->email }}</span>
                </div>
                <hr class="my-4">
                <div class="flex justify-between text-lg font-semibold">
                    <span>Total à payer</span>
                    <span class="text-blue-600">{{ number_format($subscription->amount, 0, ',', ' ') }} XOF</span>
                </div>
            </div>
        </div>

        @if($subscription->payment_status === 'paid')
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-900">Abonnement activé</h3>
                    <p class="text-sm text-green-700">Votre abonnement a été activé avec succès.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    Voir mes abonnements
                </a>
            </div>
        </div>
        @else
        <!-- Sélection de la méthode de paiement -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Choisissez votre méthode de paiement</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" id="paymentMethodsContainer">
                <!-- Mobile Money -->
                <label class="payment-method-card cursor-pointer border-2 rounded-lg p-4 hover:border-blue-500 transition-colors" data-method="MOBILE_MONEY">
                    <input type="radio" name="payment_method" value="MOBILE_MONEY" class="sr-only" required>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Mobile Money</h4>
                            <p class="text-sm text-gray-500">Orange, MTN, Moov, Wave</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 payment-method-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </label>

                <!-- Carte bancaire -->
                <label class="payment-method-card cursor-pointer border-2 rounded-lg p-4 hover:border-blue-500 transition-colors" data-method="CREDIT_CARD">
                    <input type="radio" name="payment_method" value="CREDIT_CARD" class="sr-only" required>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Carte bancaire</h4>
                            <p class="text-sm text-gray-500">Visa, Mastercard</p>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 payment-method-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </label>

                <!-- Toutes les méthodes -->
                <label class="payment-method-card cursor-pointer border-2 rounded-lg p-4 hover:border-blue-500 transition-colors border-blue-500 bg-blue-50" data-method="ALL">
                    <input type="radio" name="payment_method" value="ALL" class="sr-only" checked required>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Toutes les méthodes</h4>
                            <p class="text-sm text-gray-500">Choisir sur la plateforme</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-600 payment-method-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </label>
            </div>

            <!-- Bouton de paiement -->
            <div class="text-center">
                <button id="payButton" onclick="initiatePayment()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-8 rounded-lg text-lg transition-colors duration-200 flex items-center justify-center mx-auto space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span>Payer {{ number_format($subscription->amount, 0, ',', ' ') }} XOF</span>
                </button>
                <p class="text-sm text-gray-500 mt-3">Paiement sécurisé et crypté</p>
                <p class="text-xs text-gray-400 mt-2">Vous serez redirigé vers notre plateforme de paiement sécurisée</p>
            </div>
        </div>
        @endif

        <!-- Informations de sécurité -->
        <div class="mt-8 bg-blue-50 rounded-lg p-4">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">Paiement sécurisé</h4>
                    <p class="text-sm text-blue-700 mt-1">Vos informations de paiement sont protégées par un cryptage SSL. Nous ne stockons jamais vos données bancaires.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-method-card {
    border-color: #e5e7eb;
    background-color: #ffffff;
    transition: all 0.2s;
}

.payment-method-card:hover {
    border-color: #3b82f6;
    background-color: #f9fafb;
}

.payment-method-card:has(input:checked) {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.payment-method-check {
    display: none;
}

.payment-method-card:has(input:checked) .payment-method-check {
    display: block;
    color: #3b82f6;
}
</style>

<script>
// Gestion de la sélection des méthodes de paiement
document.addEventListener('DOMContentLoaded', function() {
    const methodCards = document.querySelectorAll('.payment-method-card');
    
    methodCards.forEach(card => {
        card.addEventListener('click', function() {
            methodCards.forEach(c => {
                c.classList.remove('border-blue-500', 'bg-blue-50');
                c.classList.add('border-gray-200', 'bg-white');
            });
            
            this.classList.remove('border-gray-200', 'bg-white');
            this.classList.add('border-blue-500', 'bg-blue-50');
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });
});

// Initialisation du paiement
function initiatePayment() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedMethod) {
        alert('Veuillez sélectionner une méthode de paiement.');
        return;
    }

    const payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Initialisation du paiement...
    `;

    const paymentData = {
        subscription_id: '{{ $subscription->id }}',
        payment_method: selectedMethod.value === 'ALL' ? null : selectedMethod.value
    };

    fetch('{{ route("subscriptions.initiate-payment") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.payment_url) {
            window.location.href = data.payment_url;
        } else {
            alert(data.message || 'Erreur lors de l\'initialisation du paiement.');
            payButton.disabled = false;
            payButton.innerHTML = `
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Payer {{ number_format($subscription->amount, 0, ',', ' ') }} XOF</span>
            `;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'initialisation du paiement. Veuillez réessayer.');
        payButton.disabled = false;
        payButton.innerHTML = `
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span>Payer {{ number_format($subscription->amount, 0, ',', ' ') }} XOF</span>
        `;
    });
}
</script>
@endsection
