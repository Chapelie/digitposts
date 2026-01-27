<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Feed extends Model
{
    use HasUuids;

    protected $fillable = [
        'isPrivate',
        'status',
        'user_id',
    ];

    protected $casts = [
        'isPrivate' => 'boolean',
    ];

    public function feedable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class, 'feed_id');
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
