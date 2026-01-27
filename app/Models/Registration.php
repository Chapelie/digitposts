<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Registration extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'feed_id',
        'feed_type',
        'status',
        'payment_status',
        'amount_paid',
        'notes',
        'platform_registration',
        'payment_method',
        'registration_data',
        'payment_transaction_id',
        'payment_url',
        'payment_date',
        'payment_details',
    ];

    protected $casts = [
        'platform_registration' => 'boolean',
        'registration_data' => 'array',
        'amount_paid' => 'decimal:2'
    ];

    // Relation avec le feed (campagne)
    public function feed()
    {
        return $this->belongsTo(Feed::class, 'feed_id');
    }

    // Relation avec l'utilisateur qui s'inscrit
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Statuts possibles
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    // Statuts de paiement
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PARTIAL = 'partial';
    public const PAYMENT_PAID = 'paid';
}
