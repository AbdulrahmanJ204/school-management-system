<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ClassSession extends Model
{
    protected $fillable = [
        'schedule_id',
        'school_day_id',
        'teacher_id',
        'subject_id',
        'section_id',
        'class_period_id',
        'status',
        'total_students_count',
        'present_students_count',
        'created_by'
    ];

    protected $casts = [
        'total_students_count' => 'integer',
        'present_students_count' => 'integer'
    ];

    // العلاقات الأساسية
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function classPeriod(): BelongsTo
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    /*public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }*/

    // العلاقات المرتبطة
    public function studentAttendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'assigned_session_id');
    }

    public function dueAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'due_session_id');
    }

    public function studyNotes(): HasMany
    {
        return $this->hasMany(StudyNote::class);
    }

    // Scopes مفيدة
    public function scopeToday($query)
    {
        return $query->whereHas('schoolDay', function($q) {
            $q->where('date', Carbon::today());
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function canBeStarted(): bool
    {
        return $this->status === 'scheduled' &&
               $this->schoolDay->date->isToday();
    }
}
