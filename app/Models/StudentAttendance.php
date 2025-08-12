<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAttendance extends Model
{
    protected $fillable = [
        'student_id',
        'school_day_id',
        'class_period_id',
        'class_session_id',
        'status',
        'created_by'
    ];

    // Relations
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class);
    }

    public function classPeriod(): BelongsTo
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
