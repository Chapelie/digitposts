<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file',
        'start_date',
        'end_date',
        'location',
        'place',
        'amount',
        'canPaid',
        'link'
    ];
    public function feed()
    {
        return $this->morphOne(Feed::class, 'feedable');
    }

}
