<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des inscrits - {{ $activity->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #1f2937;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        
        .header h1 {
            font-size: 22px;
            color: #1e40af;
            margin-bottom: 8px;
        }
        
        .header .subtitle {
            font-size: 14px;
            color: #6b7280;
        }
        
        .activity-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .activity-info h2 {
            font-size: 16px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        
        .activity-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .activity-detail {
            flex: 1;
            min-width: 150px;
        }
        
        .activity-detail .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .activity-detail .value {
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            border-collapse: collapse;
        }
        
        .stat-card {
            display: table-cell;
            width: 16.66%;
            text-align: center;
            padding: 12px 8px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        
        .stat-card .number {
            font-size: 20px;
            font-weight: 700;
        }
        
        .stat-card .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .stat-total .number { color: #3b82f6; }
        .stat-confirmed .number { color: #10b981; }
        .stat-pending .number { color: #f59e0b; }
        .stat-cancelled .number { color: #ef4444; }
        .stat-paid .number { color: #8b5cf6; }
        .stat-revenue .number { color: #059669; font-size: 14px; }
        
        .table-section h3 {
            font-size: 14px;
            color: #374151;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #1e40af;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .status-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .payment-paid {
            background: #ddd6fe;
            color: #5b21b6;
        }
        
        .payment-pending {
            background: #fed7aa;
            color: #c2410c;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
        
        .footer .logo {
            font-weight: 700;
            color: #3b82f6;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <h1>Liste des inscrits</h1>
        <div class="subtitle">{{ $activity->title }}</div>
    </div>
    
    <!-- Informations sur l'activité -->
    <div class="activity-info">
        <h2>Informations de l'activité</h2>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 5px 10px 5px 0; width: 25%;">
                    <div class="activity-detail">
                        <div class="label">Type</div>
                        <div class="value">{{ $feed->feedable_type === 'App\Models\Training' ? 'Formation' : 'Événement' }}</div>
                    </div>
                </td>
                <td style="border: none; padding: 5px 10px; width: 25%;">
                    <div class="activity-detail">
                        <div class="label">Date de début</div>
                        <div class="value">{{ \Carbon\Carbon::parse($activity->start_date)->format('d/m/Y à H:i') }}</div>
                    </div>
                </td>
                <td style="border: none; padding: 5px 10px; width: 25%;">
                    <div class="activity-detail">
                        <div class="label">Prix</div>
                        <div class="value">{{ $activity->amount > 0 ? number_format($activity->amount, 0, ',', ' ') . ' XOF' : 'Gratuit' }}</div>
                    </div>
                </td>
                <td style="border: none; padding: 5px 0 5px 10px; width: 25%;">
                    <div class="activity-detail">
                        <div class="label">Organisateur</div>
                        <div class="value">{{ $creator->firstname }} {{ $creator->lastname }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div class="number">{{ $stats['total'] }}</div>
            <div class="label">Total inscrits</div>
        </div>
        <div class="stat-card stat-confirmed">
            <div class="number">{{ $stats['confirmed'] }}</div>
            <div class="label">Confirmés</div>
        </div>
        <div class="stat-card stat-pending">
            <div class="number">{{ $stats['pending'] }}</div>
            <div class="label">En attente</div>
        </div>
        <div class="stat-card stat-cancelled">
            <div class="number">{{ $stats['cancelled'] }}</div>
            <div class="label">Annulés</div>
        </div>
        <div class="stat-card stat-paid">
            <div class="number">{{ $stats['paid'] }}</div>
            <div class="label">Payés</div>
        </div>
        <div class="stat-card stat-revenue">
            <div class="number">{{ number_format($stats['totalRevenue'], 0, ',', ' ') }} XOF</div>
            <div class="label">Revenus</div>
        </div>
    </div>
    
    <!-- Liste des inscrits -->
    <div class="table-section">
        <h3>Liste complète des inscrits ({{ $registrations->count() }})</h3>
        
        @if($registrations->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 20%;">Nom complet</th>
                        <th style="width: 20%;">Email</th>
                        <th style="width: 12%;">Téléphone</th>
                        <th style="width: 13%;">Organisation</th>
                        <th style="width: 10%;">Statut</th>
                        <th style="width: 10%;">Paiement</th>
                        <th style="width: 10%;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $index => $registration)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $registration->user->firstname ?? '' }} {{ $registration->user->lastname ?? '' }}</strong>
                            </td>
                            <td>{{ $registration->registration_data['email'] ?? $registration->user->email }}</td>
                            <td>{{ $registration->registration_data['phone'] ?? $registration->user->phone ?? '-' }}</td>
                            <td>{{ $registration->registration_data['organization'] ?? $registration->user->organization ?? '-' }}</td>
                            <td>
                                @if($registration->status === 'confirmed')
                                    <span class="status-badge status-confirmed">Confirmé</span>
                                @elseif($registration->status === 'pending')
                                    <span class="status-badge status-pending">En attente</span>
                                @else
                                    <span class="status-badge status-cancelled">Annulé</span>
                                @endif
                            </td>
                            <td>
                                @if($registration->payment_status === 'paid')
                                    <span class="status-badge payment-paid">Payé</span>
                                @else
                                    <span class="status-badge payment-pending">En attente</span>
                                @endif
                            </td>
                            <td>{{ $registration->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-data">
                <p>Aucune inscription pour cette activité.</p>
            </div>
        @endif
    </div>
    
    <!-- Pied de page -->
    <div class="footer">
        <p>Document généré le {{ $generatedAt->format('d/m/Y à H:i') }} par <span class="logo">DigitPosts</span></p>
        <p>© {{ date('Y') }} DigitPosts - Tous droits réservés</p>
    </div>
</body>
</html>
