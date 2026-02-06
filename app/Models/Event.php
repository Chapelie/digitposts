<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Event extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'file',
        'amount',
        'location',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function feed()
    {
        return $this->morphOne(Feed::class, 'feedable');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'categorizable', 'categorizable_id', 'category_id')
                    ->withPivot('categorizable_type');
    }

    public function attachCategories($categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            \DB::table('categorizable')->insert([
                'categorizable_id' => $this->id,
                'categorizable_type' => Event::class,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function registrations()
    {
        return $this->morphMany(Registration::class, 'feed');
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->amount > 0) {
            return number_format($this->amount, 0, ',', ' ') . ' FCFA';
        }
        return 'Gratuit';
    }

    public function getIsFreeAttribute()
    {
        return $this->amount <= 0;
    }
}
