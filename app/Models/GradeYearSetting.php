<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeYearSetting extends Model
{
    use HasFactory, SoftDeletes;

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
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

