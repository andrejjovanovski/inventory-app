<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        "mentor_id",
        "title",
        "attendance_date",
        "start_time",
        "end_time",
        "group_id",
        "created_by",
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, "mentor_id");
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, "group_id");
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'attendance_member')
            ->withPivot('is_present')
            ->withTimestamps();
    }
}
