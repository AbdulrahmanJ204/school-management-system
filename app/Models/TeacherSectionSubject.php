<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSectionSubject extends Model
{
    protected $fillable = [
        'teacher_id',
        'grade_id',
        'subject_id',
        'section_id',
        'is_active',
        'num_class_period',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
