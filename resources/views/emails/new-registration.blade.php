<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouvelle inscription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .activity-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .participant-info {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Nouvelle inscription !</h1>
        <p>Quelqu'un s'est inscrit à votre activité</p>
    </div>

    <div class="content">
        <h2>Bonjour {{ $owner->name }},</h2>
        
        <p>Vous avez reçu une nouvelle inscription pour votre activité :</p>

        <div class="activity-card">
            <h3>{{ $activity->title }}</h3>
            <p><strong>Type :</strong> {{ $activity instanceof \App\Models\Training ? 'Formation' : 'Événement' }}</p>
            <p><strong>Date :</strong> 
                @if($activity instanceof \App\Models\Training && $activity->end_date)
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($activity->end_date)->format('d/m/Y') }}
                @else
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y H:i') }}
                @endif
            </p>
            @if($activity->location)
                <p><strong>Lieu :</strong> {{ $activity->location }} @if($activity->place)({{ $activity->place }})@endif</p>
            @endif
            <p><strong>Prix :</strong> {{ $activity->formatted_price }}</p>
        </div>

        <h3>Informations du participant :</h3>
        <div class="participant-info">
            <p><strong>Nom :</strong> {{ $participant->name }}</p>
            <p><strong>Email :</strong> {{ $participant->email }}</p>
            @if($registration->registration_data['phone'])
                <p><strong>Téléphone :</strong> {{ $registration->registration_data['phone'] }}</p>
            @endif
            @if($registration->registration_data['organization'])
                <p><strong>Organisation :</strong> {{ $registration->registration_data['organization'] }}</p>
            @endif
            @if($registration->notes)
                <p><strong>Notes :</strong> {{ $registration->notes }}</p>
            @endif
            <p><strong>Statut :</strong> 
                @if($registration->status === 'confirmed')
                    ✅ Confirmé
                @elseif($registration->status === 'pending')
                    ⏳ En attente
                @else
                    ❌ Annulé
                @endif
            </p>
            <p><strong>Paiement :</strong> 
                @if($registration->payment_status === 'complete')
                    ✅ Payé
                @elseif($registration->payment_status === 'pending')
                    ⏳ En attente de paiement
                @else
                    ❌ Non payé
                @endif
            </p>
            @if($registration->payment_method)
                <p><strong>Méthode de paiement :</strong> 
                    @switch($registration->payment_method)
                        @case('mobile_money')
                            Mobile Money
                            @break
                        @case('card')
                            Carte bancaire
                            @break
                        @case('cash')
                            Paiement sur place
                            @break
                        @default
                            {{ $registration->payment_method }}
                    @endswitch
                </p>
            @endif
        </div>

        <p><strong>Inscription sur plateforme :</strong> 
            @if($registration->platform_registration)
                ✅ Oui - Le participant recevra des notifications
            @else
                ❌ Non - Inscription privée uniquement
            @endif
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('user.registrations') }}" class="btn">Voir toutes mes inscriptions</a>
        </div>

        <p><strong>Prochaines étapes :</strong></p>
        <ul>
            @if($registration->payment_status === 'pending')
                <li>Attendre le paiement du participant</li>
            @endif
            <li>Confirmer l'inscription si nécessaire</li>
            <li>Préparer les détails de l'activité</li>
            <li>Envoyer les informations pratiques au participant</li>
        </ul>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement par DigiPosts</p>
        <p>Si vous avez des questions, contactez-nous à support@digiposts.com</p>
    </div>
</body>
</html> 