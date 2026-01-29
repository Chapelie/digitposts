<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'plan_type',
        'start_date',
        'end_date',
        'amount',
        'payment_status',
        'payment_transaction_id',
        'payment_url',
        'payment_date',
        'payment_details',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'payment_details' => 'array',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Vérifier si l'abonnement est actif
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' 
            && $this->payment_status === 'paid'
            && $this->end_date->isFuture();
    }

    /**
     * Vérifier si l'abonnement est expiré
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->isPast() || $this->status === 'expired';
    }

    /**
     * Créer un nouvel abonnement à partir d'un plan
     */
    public static function createFromPlan($userId, \App\Models\SubscriptionPlan $plan): self
    {
        $startDate = now();
        $endDate = $startDate->copy()->addWeeks((int) $plan->duration_weeks);

        return self::create([
            'user_id' => $userId,
            'plan_type' => $plan->type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'amount' => $plan->amount,
            'payment_status' => 'pending',
            'status' => 'active',
        ]);
    }

    /**
     * Obtenir l'abonnement actif d'un utilisateur pour un type de plan
     */
    public static function getActiveSubscription($userId, ?string $planType = null): ?self
    {
        $query = self::where('user_id', $userId)
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now());

        if ($planType !== null) {
            $query->where('plan_type', $planType);
        }

        return $query->orderBy('end_date', 'desc')->first();
    }

    /**
     * Vérifier si un utilisateur a un abonnement actif (optionnellement pour un type)
     */
    public static function hasActiveSubscription($userId, ?string $planType = null): bool
    {
        return self::getActiveSubscription($userId, $planType) !== null;
    }

    /**
     * Marquer l'abonnement comme payé
     */
    public function markAsPaid($transactionId, $paymentDetails = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_transaction_id' => $transactionId,
            'payment_date' => now(),
            'payment_details' => $paymentDetails,
            'status' => 'active',
        ]);
    }

    /**
     * Marquer l'abonnement comme expiré
     */
    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }
}
