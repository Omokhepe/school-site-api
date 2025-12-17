<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        "subject",
        "message",
        "start_date",
        "end_date",
        "created_by",
    ];

    protected $casts = [
        "start_date"=> "date",
        "end_date"=> "date"
    ];
}