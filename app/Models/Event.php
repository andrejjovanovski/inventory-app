<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'max_attendees',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class);
    }
}
