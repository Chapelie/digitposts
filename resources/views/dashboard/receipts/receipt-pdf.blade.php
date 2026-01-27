<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de paiement - {{ $registration->payment_transaction_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; line-height: 1.5; padding: 24px; }
        .header { border-bottom: 3px solid #3b82f6; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; color: #1e40af; }
        .header .ref { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 13px; font-weight: bold; color: #374151; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e5e7eb; }
        .row { display: table; width: 100%; margin-bottom: 6px; }
        .label { display: table-cell; width: 40%; font-weight: bold; color: #6b7280; }
        .value { display: table-cell; }
        .activity-box { background: #f8fafc; padding: 12px; border-radius: 6px; margin-top: 8px; border-left: 4px solid #3b82f6; }
        .amount { font-size: 18px; font-weight: bold; color: #059669; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reçu de paiement</h1>
        <div class="ref">Réf. transaction : {{ $registration->payment_transaction_id }}</div>
        <div class="ref">Émis le {{ $generatedAt->format('d/m/Y à H:i') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Participant</div>
        <div class="row"><span class="label">Nom :</span><span class="value">{{ $registration->user->firstname }} {{ $registration->user->lastname }}</span></div>
        <div class="row"><span class="label">Email :</span><span class="value">{{ $registration->user->email }}</span></div>
        @if($registration->user->phone)
        <div class="row"><span class="label">Téléphone :</span><span class="value">{{ $registration->user->phone }}</span></div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Activité payée</div>
        <div class="activity-box">
            <div class="row"><span class="label">Activité :</span><span class="value">{{ $activity->title }}</span></div>
            <div class="row"><span class="label">Type :</span><span class="value">{{ $activity instanceof \App\Models\Training ? 'Formation' : 'Événement' }}</span></div>
            <div class="row"><span class="label">Date :</span><span class="value">
                @if($activity instanceof \App\Models\Training && $activity->end_date)
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($activity->end_date)->format('d/m/Y') }}
                @else
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y H:i') }}
                @endif
            </span></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Paiement</div>
        <div class="row"><span class="label">Montant payé :</span><span class="value amount">{{ number_format($registration->amount_paid ?? 0, 0, ',', ' ') }} XOF</span></div>
        <div class="row"><span class="label">Date de paiement :</span><span class="value">{{ $registration->payment_date ? \Carbon\Carbon::parse($registration->payment_date)->format('d/m/Y à H:i') : '—' }}</span></div>
        <div class="row"><span class="label">Réf. transaction :</span><span class="value">{{ $registration->payment_transaction_id ?? '—' }}</span></div>
    </div>

    <div class="footer">
        Ce document atteste du paiement effectué pour l'inscription à l'activité ci-dessus.<br>
        {{ config('app.name') }} — {{ $generatedAt->format('d/m/Y') }}
    </div>
</body>
</html>
