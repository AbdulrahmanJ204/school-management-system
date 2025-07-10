<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolDay extends Model
{
    use HasFactory;

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
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function behaviorNotes()
    {
        return $this->hasMany(BehaviorNote::class);
    }

    public function studyNotes()
    {
        return $this->hasMany(StudyNote::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class);
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

