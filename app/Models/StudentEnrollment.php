<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'semester_id',
        'created_by'
    ];

    // Relations
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentMarks()
    {
        return $this->hasMany(StudentMark::class, 'enrollment_id');
    }

    // Accessors
    public function getGradeAttribute()
    {
        return $this->section->grade;
    }

    public function getYearAttribute()
    {
        return $this->semester->year;
    }

    public function getUserAttribute()
    {
        return $this->student->user;
    }

    // Methods
    public function getSubjectsWithMarks()
    {
        return $this->studentMarks()
            ->with('subject.subjectMajor')
            ->get()
            ->groupBy('subject.subject_major_id');
    }

    public function getTotalMarks()
    {
        return $this->studentMarks()->sum('total');
    }

    public function getAverageMarks()
    {
        $marks = $this->studentMarks()->whereNotNull('total');
        return $marks->count() > 0 ? $marks->avg('total') : 0;
    }

    public function getFailedSubjects()
    {
        return $this->studentMarks()
            ->with('subject.subjectMajor')
            ->get()
            ->filter(function($mark) {
                return !$mark->isPass();
            });
    }

    public function isPromoted()
    {
        $failedSubjects = $this->getFailedSubjects();
        $gradeYear = GradeYearSetting::where('year_id', $this->semester->year_id)
            ->where('grade_id', $this->section->grade_id)
            ->first();

        if (!$gradeYear) {
            return false;
        }

        return $failedSubjects->count() <= $gradeYear->max_failed_subjects;
    }

    // Scopes
    public function scopeForSemester($query, $semesterId)
    {
        return $query->where('semester_id', $semesterId);
    }

    public function scopeForSection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeForGrade($query, $gradeId)
    {
        return $query->whereHas('section', function($q) use ($gradeId) {
            $q->where('grade_id', $gradeId);
        });
    }

    public function scopeForYear($query, $yearId)
    {
        return $query->whereHas('semester', function($q) use ($yearId) {
            $q->where('year_id', $yearId);
        });
    }
}

