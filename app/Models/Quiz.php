<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['name', 'created_by', 'is_active', 'taken_at', 'full_score', 'quiz_photo'];
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    public function scores()
    {
        return $this->hasMany(ScoreQuiz::class);
    }
    public function targets()
    {
        return $this->hasMany(QuizTarget::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
