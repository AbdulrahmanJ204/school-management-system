<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_day_id',
        'grade_id',
        'main_subject_id',
        'created_by'
    ];

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    public function mainSubject(): BelongsTo
    {
        return $this->belongsTo(MainSubject::class, 'main_subject_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 