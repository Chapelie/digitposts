<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Votre reçu de paiement</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%); color: white; padding: 24px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f8f9fa; padding: 24px; border-radius: 0 0 10px 10px; }
        .card { background: white; border-radius: 8px; padding: 16px; margin: 16px 0; border-left: 4px solid #3b82f6; }
        .amount { font-size: 20px; font-weight: bold; color: #059669; }
        .footer { text-align: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reçu de paiement</h1>
        <p>Votre paiement a bien été enregistré</p>
    </div>

    <div class="content">
        <p>Bonjour {{ $user->firstname }} {{ $user->lastname }},</p>
        <p>Vous trouverez en pièce jointe votre reçu au format PDF pour l'activité suivante :</p>

        <div class="card">
            <strong>{{ $activity->title }}</strong><br>
            Type : {{ $activity instanceof \App\Models\Training ? 'Formation' : 'Événement' }}<br>
            Montant payé : <span class="amount">{{ number_format($registration->amount_paid ?? 0, 0, ',', ' ') }} XOF</span><br>
            Date de paiement : {{ $registration->payment_date ? \Carbon\Carbon::parse($registration->payment_date)->format('d/m/Y à H:i') : '' }}
        </div>

        <p>Conservez ce reçu pour vos archives.</p>
    </div>

    <div class="footer">
        {{ config('app.name') }} — Reçu envoyé automatiquement
    </div>
</body>
</html>
