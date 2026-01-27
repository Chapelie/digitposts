<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Category extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'type',
        'categorizable_id',
        'categorizable_type',
    ];

    public function categorizable(): MorphTo
    {
        return $this->morphTo();
    }
}
