<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionPlan extends Model
{
    use HasUuids;

    public const TYPE_FREE_EVENTS = 'free_events';
    public const TYPE_CREATE_ACTIVITIES = 'create_activities';

    protected $fillable = [
        'type',
        'name',
        'description',
        'amount',
        'duration_weeks',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'duration_weeks' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Récupérer un plan par type
     */
    public static function getByType(string $type): ?self
    {
        return self::where('type', $type)->where('is_active', true)->first();
    }

    /**
     * Plans pour événements gratuits
     */
    public static function freeEventsPlan(): ?self
    {
        return self::getByType(self::TYPE_FREE_EVENTS);
    }

    /**
     * Plan pour créer des activités
     */
    public static function createActivitiesPlan(): ?self
    {
        return self::getByType(self::TYPE_CREATE_ACTIVITIES);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 0, ',', ' ') . ' XOF';
    }
}
