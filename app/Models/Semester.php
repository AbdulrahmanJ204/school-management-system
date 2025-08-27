<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'year_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];
    protected $with =[
        'year',
    ];


    // Relations
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function schoolDays(): HasMany
    {
        return $this->hasMany(SchoolDay::class);
    }

    public function studentEnrollments(): HasMany
    {
        return $this->hasMany(StudentEnrollment::class);
    }

    public function quizTargets(): HasMany
    {
        return $this->hasMany(QuizTarget::class);
    }


    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

