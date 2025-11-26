<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
