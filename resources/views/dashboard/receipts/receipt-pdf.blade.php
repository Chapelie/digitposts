<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de paiement - {{ $registration->payment_transaction_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'DejaVu Sans', sans-serif; 
            font-size: 11px; 
            color: #1f2937; 
            line-height: 1.6; 
            padding: 0;
            background: #ffffff;
        }
        
        /* Header avec logo et titre */
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%);
            color: white;
            padding: 30px 40px;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .logo-section .app-name {
            font-size: 26px;
            font-weight: bold;
            color: white;
            letter-spacing: 1px;
        }
        
        .receipt-title {
            text-align: right;
        }
        
        .receipt-title h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            color: white;
        }
        
        .receipt-title .subtitle {
            font-size: 12px;
            opacity: 0.9;
            color: white;
        }
        
        /* Informations principales */
        .main-info {
            padding: 0 40px;
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 10px 15px;
            font-weight: 600;
            color: #6b7280;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        
        .info-value {
            display: table-cell;
            padding: 10px 15px;
            color: #1f2937;
            border: 1px solid #e5e7eb;
        }
        
        /* Section activité */
        .activity-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 0 40px 30px 40px;
        }
        
        .activity-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #3b82f6;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: bold;
        }
        
        .activity-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            flex: 1;
        }
        
        .activity-type {
            display: inline-block;
            padding: 4px 12px;
            background: #3b82f6;
            color: white;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .activity-details {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .activity-row {
            display: table-row;
        }
        
        .activity-label {
            display: table-cell;
            width: 30%;
            padding: 8px 0;
            font-weight: 600;
            color: #475569;
        }
        
        .activity-value {
            display: table-cell;
            padding: 8px 0;
            color: #1e293b;
        }
        
        /* Section paiement */
        .payment-section {
            background: #ffffff;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 25px;
            margin: 0 40px 30px 40px;
        }
        
        .payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #10b981;
        }
        
        .payment-title {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .amount-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            text-align: center;
            min-width: 200px;
        }
        
        .amount-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .amount-value {
            font-size: 28px;
            font-weight: bold;
            line-height: 1.2;
        }
        
        .payment-details {
            display: table;
            width: 100%;
            margin-top: 15px;
        }
        
        .payment-row {
            display: table-row;
        }
        
        .payment-label {
            display: table-cell;
            width: 40%;
            padding: 8px 0;
            font-weight: 600;
            color: #6b7280;
        }
        
        .payment-value {
            display: table-cell;
            padding: 8px 0;
            color: #1f2937;
            font-weight: 500;
        }
        
        .transaction-id {
            font-family: 'Courier New', monospace;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 4px;
            color: #1f2937;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            padding: 25px 40px;
            background: #f9fafb;
            border-top: 3px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        
        .footer-content {
            margin-bottom: 10px;
        }
        
        .footer-legal {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 10px;
            line-height: 1.5;
        }
        
        /* Badge de statut */
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            background: #10b981;
            color: white;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Séparateur */
        .divider {
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
            margin: 20px 0;
        }
        
        /* Responsive pour PDF */
        @page {
            margin: 0;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header avec logo -->
    <div class="header">
        <div class="header-content">
            <div class="logo-section">
                <div class="app-name">DigitPosts</div>
            </div>
            <div class="receipt-title">
                <h1>REÇU DE PAIEMENT</h1>
                <div class="subtitle">Document officiel</div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="main-info">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Référence transaction</div>
                <div class="info-value">
                    <span class="transaction-id">{{ $registration->payment_transaction_id ?? $registration->id }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Date d'émission</div>
                <div class="info-value">{{ $generatedAt->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut</div>
                <div class="info-value">
                    <span class="status-badge">Payé</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Participant -->
    <div class="main-info">
        <div style="font-size: 14px; font-weight: bold; color: #1e40af; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #3b82f6;">
            Informations du participant
        </div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom complet</div>
                <div class="info-value">{{ $registration->user->firstname }} {{ $registration->user->lastname }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $registration->user->email }}</div>
            </div>
            @if($registration->user->phone)
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $registration->user->phone }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Section Activité -->
    <div class="activity-section">
        <div class="activity-header">
            <div class="activity-icon">
                {{ $activity instanceof \App\Models\Training ? 'F' : 'E' }}
            </div>
            <div class="activity-title">{{ $activity->title }}</div>
            <span class="activity-type">{{ $activity instanceof \App\Models\Training ? 'Formation' : 'Événement' }}</span>
        </div>
        
        <div class="activity-details">
            <div class="activity-row">
                <div class="activity-label">Description</div>
                <div class="activity-value">{{ mb_substr(strip_tags($activity->description ?? '—'), 0, 100) }}{{ mb_strlen(strip_tags($activity->description ?? '')) > 100 ? '...' : '' }}</div>
            </div>
            <div class="activity-row">
                <div class="activity-label">Date de début</div>
                <div class="activity-value">
                    {{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y à H:i') }}
                </div>
            </div>
            @if($activity instanceof \App\Models\Training && $activity->end_date)
            <div class="activity-row">
                <div class="activity-label">Date de fin</div>
                <div class="activity-value">
                    {{ \Carbon\Carbon::parse($activity->end_date)->format('d/m/Y à H:i') }}
                </div>
            </div>
            @endif
            @if($activity->location ?? $activity->place ?? null)
            <div class="activity-row">
                <div class="activity-label">Lieu</div>
                <div class="activity-value">{{ $activity->location ?? $activity->place }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Section Paiement -->
    <div class="payment-section">
        <div class="payment-header">
            <div class="payment-title">
                Détails du paiement
            </div>
            <div class="amount-box">
                <div class="amount-label">Montant payé</div>
                <div class="amount-value">{{ number_format($registration->amount_paid ?? 0, 0, ',', ' ') }} XOF</div>
            </div>
        </div>
        
        <div class="payment-details">
            <div class="payment-row">
                <div class="payment-label">Date de paiement</div>
                <div class="payment-value">
                    {{ $registration->payment_date ? \Carbon\Carbon::parse($registration->payment_date)->format('d/m/Y à H:i') : '—' }}
                </div>
            </div>
            <div class="payment-row">
                <div class="payment-label">Méthode de paiement</div>
                <div class="payment-value">Paiement mobile</div>
            </div>
            <div class="payment-row">
                <div class="payment-label">Référence transaction</div>
                <div class="payment-value">
                    <span class="transaction-id">{{ $registration->payment_transaction_id ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <strong>DigitPosts</strong> — Plateforme de formations et événements professionnels au Burkina Faso
        </div>
        <div class="footer-legal">
            Ce document atteste du paiement effectué pour l'inscription à l'activité mentionnée ci-dessus.<br>
            Ce reçu est valable à des fins comptables et fiscales.<br>
            Document généré le {{ $generatedAt->format('d/m/Y à H:i') }} — {{ config('app.url') }}
        </div>
    </div>
</body>
</html>
