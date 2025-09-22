<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'class_session_id',
        'teacher_id',
        'status',
        'created_by'
    ];

    // Relations
    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
