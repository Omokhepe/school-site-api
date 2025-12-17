<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteContent extends Model
{
    protected $fillable = [
        "note_id",
        "subtitle",
        "content",
        "type",
        "image_path",
        "order",
    ];

    public function note(){
        return $this->belongsTo(Note::class);
    }
}