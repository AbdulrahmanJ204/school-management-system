<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'enrollment_id',
        'homework',
        'oral',
        'activity',
        'quiz',
        'exam',
        'total',
        'created_by'
    ];

    protected $casts = [
        'homework' => 'integer',
        'oral' => 'integer',
        'activity' => 'integer',
        'quiz' => 'integer',
        'exam' => 'integer',
        'total' => 'integer'
    ];

    // Relations
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getStudentAttribute()
    {
        return $this->enrollment->student;
    }

    public function getSectionAttribute()
    {
        return $this->enrollment->section;
    }

    public function getSemesterAttribute()
    {
        return $this->enrollment->semester;
    }

    // Methods
    public function calculateTotal(): float|int
    {
        $subject = $this->subject;
        $total = 0;

        if ($this->homework) {
            $total += ($this->homework * $subject->homework_percentage) / 100;
        }
        if ($this->oral) {
            $total += ($this->oral * $subject->oral_percentage) / 100;
        }
        if ($this->activity) {
            $total += ($this->activity * $subject->activity_percentage) / 100;
        }
        if ($this->quiz) {
            $total += ($this->quiz * $subject->quiz_percentage) / 100;
        }
        if ($this->exam) {
            $total += ($this->exam * $subject->exam_percentage) / 100;
        }

        return $total;
    }
}

