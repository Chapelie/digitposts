<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
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
