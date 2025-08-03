<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'class_period_id',
        'teacher_section_subject_id',
        'timetable_id',
        'week_day',
    ];
    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }
    public function classPeriod()
    {
        return $this->belongsTo(ClassPeriod::class);
    }
    public function teacherSectionSubject()
    {
        return $this->belongsTo(TeacherSectionSubject::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
