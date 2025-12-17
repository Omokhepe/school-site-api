<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id','parent_first_name','parent_last_name',
        'parent_email','parent_phone','parent_address'
    ];
}