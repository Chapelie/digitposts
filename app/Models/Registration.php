<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'feed_id',
        'feed_type',
        'status',
        'payment_status',
        'amount_paid',
        'notes'
    ];

    // Relation polymorphique avec les campagnes (Training ou Event)
    public function feed()
    {
        return $this->morphTo();
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
    public const PAYMENT_COMPLETE = 'complete';
}
