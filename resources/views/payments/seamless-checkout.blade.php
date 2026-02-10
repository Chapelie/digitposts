@extends('layouts.app')

@section('title', 'Paiement - ' . ($registration->feed->feedable->title ?? 'Inscription'))

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Finaliser votre inscription</h1>
            <p class="text-lg text-gray-600">Paiement sécurisé</p>
        </div>

        <!-- Détails de la formation -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $registration->feed->feedable->title }}</h2>
                    <p class="text-gray-600 mb-4">{{ Str::limit($registration->feed->feedable->description ?? '', 150) }}</p>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Date :</span>
                            <span class="text-gray-600">
                                @if($registration->feed->feedable_type === 'App\Models\Training' && $registration->feed->feedable->end_date)
                                    {{ \Carbon\Carbon::parse($registration->feed->feedable->start_date)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($registration->feed->feedable->end_date)->format('d/m/Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($registration->feed->feedable->start_date)->format('d/m/Y H:i') }}
                                @endif
                            </span>
                        </div>
                        @if($registration->feed->feedable->location)
                        <div>
                            <span class="font-medium text-gray-700">Lieu :</span>
                            <span class="text-gray-600">{{ $registration->feed->feedable->location }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Résumé du paiement -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé de votre commande</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Activité</span>
                    <span class="font-medium">{{ $registration->feed->feedable->title }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Participant</span>
                    <span class="font-medium">{{ $registration->user->firstname }} {{ $registration->user->lastname }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Email</span>
                    <span class="font-medium">{{ $registration->user->email }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Téléphone</span>
                    <span class="font-medium">{{ $registration->user->phone ?? 'Non renseigné' }}</span>
                </div>
                <hr class="my-4">
                <div class="flex justify-between text-lg font-semibold">
                    <span>Total à payer</span>
                    <span class="text-blue-600">{{ $registration->feed->feedable->formatted_price }}</span>
                </div>
            </div>
        </div>

        <!-- Statut du paiement -->
        @if($registration->payment_status === 'paid')
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold text-green-900">Paiement confirmé</h3>
                    <p class="text-sm text-green-700">Votre inscription a été confirmée avec succès.</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('user.registrations') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    Voir mes inscriptions
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

                <!-- Wallet -->
                <label class="payment-method-card cursor-pointer border-2 rounded-lg p-4 hover:border-blue-500 transition-colors" data-method="WALLET">
                    <input type="radio" name="payment_method" value="WALLET" class="sr-only" required>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">Portefeuille</h4>
                            <p class="text-sm text-gray-500">Wallet numérique</p>
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
                    <span>Payer {{ $registration->feed->feedable->formatted_price }}</span>
                </button>
                <p class="text-sm text-gray-500 mt-3">Paiement sécurisé et crypté</p>
                <p class="text-xs text-gray-400 mt-2">Le guichet CinetPay s'ouvrira pour finaliser le paiement</p>
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

.payment-method-card input:checked + div {
    border-color: #3b82f6;
    background-color: #eff6ff;
}

.payment-method-card input:checked ~ .payment-method-check {
    color: #3b82f6;
    display: block;
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

@section('head')
<script src="https://cdn.cinetpay.com/seamless/main.js" type="text/javascript"></script>
@endsection

@php
    $cinetpayBase = rtrim(config('cinetpay.payment_base_url') ?? config('app.url'), '/');
    $cinetpayNotifyUrl = $cinetpayBase . '/' . ltrim(route('payments.notify', [], false), '/');
@endphp
<script>
window.cinetpayConfig = {
    apikey: @json(config('cinetpay.api_key')),
    site_id: @json(config('cinetpay.site_id')),
    notify_url: @json($cinetpayNotifyUrl)
};

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
            if (radio) radio.checked = true;
        });
    });
    const allMethodsCard = document.querySelector('[data-method="ALL"]');
    if (allMethodsCard) allMethodsCard.classList.add('border-blue-500', 'bg-blue-50');
});

function initiatePayment() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedMethod) {
        showNotification('error', 'Veuillez sélectionner une méthode de paiement.');
        return;
    }

    const payButton = document.getElementById('payButton');
    payButton.disabled = true;
    payButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Initialisation...';

    const payload = {
        registration_id: '{{ $registration->id }}',
        amount: {{ (int) ($registration->feed->feedable->amount ?? 0) }},
        payment_method: selectedMethod.value === 'ALL' ? null : selectedMethod.value
    };

    fetch('{{ route("payments.initiate") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            showNotification('error', data.message || 'Erreur lors de l\'initialisation.');
            resetPayButton();
            return;
        }
        const cfg = window.cinetpayConfig;
        if (!cfg || !cfg.apikey || !cfg.site_id) {
            showNotification('error', 'Configuration CinetPay manquante.');
            resetPayButton();
            return;
        }
        CinetPay.setConfig({
            apikey: cfg.apikey,
            site_id: cfg.site_id,
            notify_url: cfg.notify_url,
            close_after_response: true
        });
        CinetPay.getCheckout({
            transaction_id: data.transaction_id,
            amount: data.amount,
            currency: data.currency,
            channels: data.channels,
            description: data.description,
            customer_name: data.customer_name,
            customer_surname: data.customer_surname,
            customer_email: data.customer_email,
            customer_phone_number: data.customer_phone_number || '',
            customer_address: data.customer_address,
            customer_city: data.customer_city,
            customer_country: data.customer_country,
            customer_state: data.customer_state,
            customer_zip_code: data.customer_zip_code || ''
        });
        CinetPay.waitResponse(function(res) {
            if (res.status === 'REFUSED') {
                showNotification('error', 'Votre paiement a échoué.');
                resetPayButton();
            } else if (res.status === 'ACCEPTED') {
                showNotification('success', 'Paiement effectué avec succès !');
                setTimeout(function() { window.location.href = '{{ route("payments.after-success") }}'; }, 1500);
            }
        });
        CinetPay.onClose(function(res) {
            if (res && res.status === 'REFUSED') {
                showNotification('error', 'Votre paiement a échoué.');
            } else if (res && res.status === 'ACCEPTED') {
                showNotification('success', 'Paiement effectué avec succès !');
                setTimeout(function() { window.location.href = '{{ route("payments.after-success") }}'; }, 1500);
                return;
            }
            resetPayButton();
        });
        CinetPay.onError(function(err) {
            console.error('CinetPay error', err);
            showNotification('error', 'Erreur du guichet de paiement.');
            resetPayButton();
        });
    })
    .catch(function(err) {
        console.error(err);
        showNotification('error', 'Erreur lors de l\'initialisation. Veuillez réessayer.');
        resetPayButton();
    });
}

function resetPayButton() {
    const payButton = document.getElementById('payButton');
    if (!payButton) return;
    payButton.disabled = false;
    payButton.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg><span>Payer {{ $registration->feed->feedable->formatted_price }}</span>';
}

function showNotification(type, message) {
    var notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ' +
        (type === 'success' ? 'bg-green-500 text-white' : type === 'error' ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white');
    notification.innerHTML = '<div class="flex items-center space-x-2"><span>' + message + '</span></div>';
    document.body.appendChild(notification);
    setTimeout(function() { notification.remove(); }, 5000);
}
</script>
@endsection 