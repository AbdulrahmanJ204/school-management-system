<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreQuiz extends Model
{
    protected $table    = 'score_quizzes';
    protected $fillable = ['quiz_id','student_id','score','taken_at'];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'quiz_id');
    }
    public function student()
    {
        return $this->belongsTo(Student::class,'student_id');
    }
}
