<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['quiz_id','question_text','question_photo','choices',
        'right_choice','hint','hint_photo','order','question_text_plain','choices_count'];
    protected $casts = [
        'question_text' => 'array',
        'choices' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'quiz_id');
    }
}
