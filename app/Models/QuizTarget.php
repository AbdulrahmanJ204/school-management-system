<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'subject_id',
        'section_id',
        'semester_id',
        'grade_id'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
}
