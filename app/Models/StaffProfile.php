<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    protected $fillable = [
        'user_id','highest_education','course',
        'reference_name','reference_phone','reference_email'
    ];
}