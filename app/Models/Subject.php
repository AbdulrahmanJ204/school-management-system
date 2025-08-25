<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'main_subject_id',
        'code',
        'full_mark',
        'homework_percentage',
        'oral_percentage',
        'activity_percentage',
        'quiz_percentage',
        'exam_percentage',
        'num_class_period',
        'is_failed',
        'created_by'
    ];

    protected $casts = [
        'full_mark' => 'integer',
        'homework_percentage' => 'integer',
        'oral_percentage' => 'integer',
        'activity_percentage' => 'integer',
        'quiz_percentage' => 'integer',
        'exam_percentage' => 'integer',
        'num_class_period' => 'integer',
        'is_failed' => 'boolean'
    ];

    // Relations
    public function mainSubject(): BelongsTo
    {
        return $this->belongsTo(MainSubject::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teacherSectionSubjects(): HasMany
    {
        return $this->hasMany(TeacherSectionSubject::class);
    }

    public function quizTargets(): HasMany
    {
        return $this->hasMany(QuizTarget::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function studentMarks(): HasMany
    {
        return $this->hasMany(StudentMark::class);
    }

    public function studyNotes(): HasMany
    {
        return $this->hasMany(StudyNote::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function getGrade()
    {
        return $this->mainSubject ? $this->mainSubject->grade : null;
    }
    // Func

    public function TotalPercentage()
    {
        return $this->homework_percentage + $this->oral_percentage +
            $this->activity_percentage + $this->quiz_percentage +
            $this->exam_percentage;
    }
}

