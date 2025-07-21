<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'created_by'
    ];

    // Relations
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function subjectMajors()
    {
        return $this->hasMany(SubjectMajor::class);
    }

    public function settingGradeYears()
    {
        return $this->hasMany(GradeYearSetting::class);
    }
}

