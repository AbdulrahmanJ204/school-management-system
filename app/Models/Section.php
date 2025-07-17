<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'grade_id',
        'created_by'
    ];

    // Relations
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function quizTargets()
    {
        return $this->hasMany(QuizTarget::class);
    }
}

