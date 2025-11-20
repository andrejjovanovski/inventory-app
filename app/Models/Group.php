<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function members()
    {
        return $this->belongsToMany(Member::class, 'group_member', 'group_id', 'member_id');
    }
}
