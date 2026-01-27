<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Inscriptions - {{ $user->firstname }} {{ $user->lastname }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .user-info h2 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #1f2937;
        }
        
        .user-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .user-info-label {
            font-weight: bold;
            color: #6b7280;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            gap: 15px;
        }
        
        .stat-box {
            background: #f8f9fa;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            flex: 1;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        .table-container {
            margin-bottom: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        thead {
            background: #3b82f6;
            color: white;
        }
        
        th {
            padding: 12px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        tbody tr:hover {
            background: #f9fafb;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-confirmed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Mes Inscriptions</h1>
        <p>Historique complet de vos inscriptions aux activités</p>
    </div>

    <!-- User Info -->
    <div class="user-info">
        <h2>Informations du compte</h2>
        <div class="user-info-row">
            <span class="user-info-label">Nom complet:</span>
            <span>{{ $user->firstname }} {{ $user->lastname }}</span>
        </div>
        <div class="user-info-row">
            <span class="user-info-label">Email:</span>
            <span>{{ $user->email }}</span>
        </div>
        @if($user->phone)
        <div class="user-info-row">
            <span class="user-info-label">Téléphone:</span>
            <span>{{ $user->phone }}</span>
        </div>
        @endif
        <div class="user-info-row">
            <span class="user-info-label">Date de génération:</span>
            <span>{{ $generatedAt->format('d/m/Y à H:i') }}</span>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{{ $registrations->count() }}</div>
            <div class="stat-label">Total inscriptions</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $confirmedCount }}</div>
            <div class="stat-label">Confirmées</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $paidCount }}</div>
            <div class="stat-label">Payées</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</div>
            <div class="stat-label">Total payé</div>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="table-container">
        @if($registrations->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Date inscription</th>
                    <th>Activité</th>
                    <th>Type</th>
                    <th class="text-right">Montant</th>
                    <th class="text-center">Statut</th>
                    <th class="text-center">Paiement</th>
                    <th>Date paiement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $registration)
                <tr>
                    <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ Str::limit($registration->feed->feedable->title, 40) }}</td>
                    <td>{{ $registration->feed->feedable_type === 'App\Models\Training' ? 'Formation' : 'Événement' }}</td>
                    <td class="text-right">
                        @if($registration->amount_paid > 0)
                            {{ number_format($registration->amount_paid, 0, ',', ' ') }} FCFA
                        @else
                            <span style="color: #059669;">Gratuit</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-{{ $registration->status }}">
                            {{ ucfirst($registration->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($registration->payment_status === 'paid')
                            <span class="badge badge-paid">Payé</span>
                        @elseif($registration->payment_status === 'pending')
                            <span class="badge badge-pending">En attente</span>
                        @elseif(in_array($registration->payment_status, ['failed', 'cancelled']))
                            <span class="badge badge-unpaid">{{ ucfirst($registration->payment_status) }}</span>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td>
                        @if($registration->payment_date)
                            {{ $registration->payment_date->format('d/m/Y H:i') }}
                        @else
                            <span style="color: #9ca3af;">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right" style="font-weight: bold;">TOTAL PAYÉ:</td>
                    <td class="text-right" style="font-weight: bold; color: #059669;">
                        {{ number_format($totalAmount, 0, ',', ' ') }} FCFA
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
        @else
        <div class="no-data">
            <p>Aucune inscription trouvée.</p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Document généré le {{ $generatedAt->format('d/m/Y à H:i') }} par DigitPosts</p>
        <p>Ce document est confidentiel et destiné uniquement à {{ $user->firstname }} {{ $user->lastname }}</p>
    </div>
</body>
</html>
