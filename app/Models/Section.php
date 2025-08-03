<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'grade_id',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relations
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function quizTargets(): HasMany
    {
        return $this->hasMany(QuizTarget::class);
    }
}

