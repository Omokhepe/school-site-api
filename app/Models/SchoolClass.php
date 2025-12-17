<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    //
    public function students()
    {
        return $this->hasMany(User::class)->where('role','student');
    }
}
