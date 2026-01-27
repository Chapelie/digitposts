@props(['registration'])

<div class="payment-status-component" data-registration-id="{{ $registration->id }}">
    <div class="flex items-center space-x-2">
        @if($registration->payment_status === 'complete')
            <div class="flex items-center text-green-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Paiement confirmé</span>
            </div>
        @elseif($registration->payment_status === 'pending')
            <div class="flex items-center text-yellow-600">
                <svg class="w-5 h-5 mr-2 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Paiement en cours</span>
            </div>
            <button onclick="checkPaymentStatus({{ $registration->id }})" 
                    class="text-blue-600 hover:text-blue-800 text-sm underline">
                Vérifier le statut
            </button>
        @elseif($registration->payment_status === 'failed')
            <div class="flex items-center text-red-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Paiement échoué</span>
            </div>
            @if($registration->payment_url)
                <a href="{{ $registration->payment_url }}" target="_blank" 
                   class="text-blue-600 hover:text-blue-800 text-sm underline">
                    Réessayer
                </a>
            @endif
        @else
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">En attente de paiement</span>
            </div>
            @if($registration->payment_url)
                <a href="{{ $registration->payment_url }}" target="_blank" 
                   class="text-blue-600 hover:text-blue-800 text-sm underline">
                    Procéder au paiement
                </a>
            @endif
        @endif
    </div>

    @if($registration->payment_transaction_id)
        <div class="text-xs text-gray-500 mt-1">
            Transaction ID: {{ $registration->payment_transaction_id }}
        </div>
    @endif

    @if($registration->payment_date)
        <div class="text-xs text-gray-500 mt-1">
            Payé le: {{ $registration->payment_date->format('d/m/Y H:i') }}
        </div>
    @endif
</div>

<script>
function checkPaymentStatus(registrationId) {
    const button = event.target;
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'Vérification...';
    
    fetch('/payment/check-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            registration_id: registrationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la page pour afficher le nouveau statut
            window.location.reload();
        } else {
            alert('Erreur lors de la vérification: ' + data.message);
            button.disabled = false;
            button.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la vérification du statut');
        button.disabled = false;
        button.textContent = originalText;
    });
}
</script> 