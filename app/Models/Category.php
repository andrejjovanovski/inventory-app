<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use softDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
