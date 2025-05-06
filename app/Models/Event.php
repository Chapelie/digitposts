<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'file',
        'amount',
    ];

    public function feed()
    {
        return $this->morphOne(Feed::class, 'feedable');
    }

    public function categories(): MorphMany
    {
        return $this->morphMany(Category::class, 'categorizable');
    }
    public function registrations()
    {
        return $this->morphMany(Registration::class, 'feed');
    }
}
