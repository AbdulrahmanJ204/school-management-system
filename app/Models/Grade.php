<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function subjectMajors(): HasMany
    {
        return $this->hasMany(SubjectMajor::class);
    }

    public function settingGradeYears(): HasMany
    {
        return $this->hasMany(GradeYearSetting::class);
    }
    public function schoolShiftTargets(): HasMany
    {
        return $this->hasMany(SchoolShiftTarget::class);
    }
}

