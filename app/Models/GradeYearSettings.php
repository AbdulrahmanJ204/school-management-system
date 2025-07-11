<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeYearSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_id',
        'max_failed_subjects',
        'help_marks',
        'grade_id',
        'created_by'
    ];

    protected $casts = [
        'max_failed_subjects' => 'integer',
        'help_marks' => 'integer'
    ];

    // Relations
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

