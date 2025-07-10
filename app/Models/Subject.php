<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject_major_id',
        'code',
        'full_mark',
        'homework_percentage',
        'oral_percentage',
        'activity_percentage',
        'quiz_percentage',
        'exam_percentage',
        'num_class_period',
        'created_by'
    ];

    protected $casts = [
        'full_mark' => 'integer',
        'homework_percentage' => 'integer',
        'oral_percentage' => 'integer',
        'activity_percentage' => 'integer',
        'quiz_percentage' => 'integer',
        'exam_percentage' => 'integer',
        'num_class_period' => 'integer'
    ];

    // Relations
    public function subjectMajor()
    {
        return $this->belongsTo(SubjectMajor::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function studentMarks()
    {
        return $this->hasMany(StudentMark::class);
    }

    public function studyNotes()
    {
        return $this->hasMany(StudyNote::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getGradeAttribute()
    {
        return $this->subjectMajor->grade;
    }

    // Func
    public function calculateTotalPercentage()
    {
        return $this->homework_percentage + $this->oral_percentage +
            $this->activity_percentage + $this->quiz_percentage +
            $this->exam_percentage;
    }
}

