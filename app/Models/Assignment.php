<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_day_id',
        'class_session_id',
        'type',
        'title',
        'description',
        'photo',
        'subject_id',
        'section_id',
        'due_date',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    // Relations
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class);
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
