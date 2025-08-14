<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'assigned_session_id',
        'due_session_id',
        'type',
        'title',
        'description',
        'photo',
        'subject_id',
        'section_id',
        'created_by'
    ];

    // Relations
    public function assignedSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'assigned_session_id');
    }

    public function dueSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'due_session_id');
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
