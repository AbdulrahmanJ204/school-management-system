<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEnrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'grade_id',
        'section_id',
        'semester_id',
        'year_id',
        'last_year_gpa',
        'created_by'
    ];

    // Relations
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentMarks(): HasMany
    {
        return $this->hasMany(StudentMark::class, 'enrollment_id');
    }

    // Accessors
    public function getGradeAttribute()
    {
        return Grade::findOrFail($this->grade_id);
    }

    public function getYearAttribute()
    {
        return $this->semester->year;
    }

    public function getUserAttribute()
    {
        return $this->student->user;
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
}

