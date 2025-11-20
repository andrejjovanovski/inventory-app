<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{

    protected $fillable = [
        'image',
        'name',
        'slug',
        'description',
        'category_id',
        'quantity',
    ];

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
