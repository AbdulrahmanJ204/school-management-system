<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizScore extends Model
{
    protected $fillable = [
        'quiz_id',
        'student_id',
        'score',
        'full_score'
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
