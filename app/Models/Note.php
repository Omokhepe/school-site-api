<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'note_id',
        'teacher_id',
        'subject_id',
        'class_id',
        'school_year',
        'term', 
        'weeks',
        'topic'
    ];

    protected $casts = [
        'weeks'=>'array',
    ];

    public function subtopics(){
        return $this-> hasMany(NoteContent::class)->orderBy('order');
    }
    public function teacher(){return $this->belongsTo(User::class, 'teacher_id');}
    public function class(){return $this->belongsTo(SchoolClass::class, 'class_id');}
    public function subject(){return $this->belongsTo(Subject::class, 'subject_id');}
}