<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $table = 'timetables';
    protected $fillable = ['class_id','subject_id','teacher_id','day','start_time','end_time'];

    public function subject() { return $this->belongsTo(Subject::class, 'subject_id'); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
    public function class()   { return $this->belongsTo(SchoolClass::class, 'class_id'); }
}