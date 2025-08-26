<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'semester_id',
        'type',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    // Relations
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function behaviorNotes(): HasMany
    {
        return $this->hasMany(BehaviorNote::class);
    }

    public function studyNotes(): HasMany
    {
        return $this->hasMany(StudyNote::class);
    }

    public function classSessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function studentAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function teacherAttendances(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    // Scopes
    public function scopeStudyDays($query)
    {
        return $query->where('type', 'study');
    }

    public function scopeExamDays($query)
    {
        return $query->where('type', 'exam');
    }
}

