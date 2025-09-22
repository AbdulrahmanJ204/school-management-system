<?php

namespace App\Models;

use App\Enums\NoteTypeEnum;
use App\Observers\StudyNoteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([StudyNoteObserver::class])]
class StudyNote extends Model
{
    use SoftDeletes;

    protected $table = 'study_notes';

    protected $fillable = [
        'student_id',
        'school_day_id',
        'subject_id',
        'note_type',
        'note',
        'marks',
        'created_by'
    ];

    protected $casts = [
        'marks' => 'integer',
        'note_type' => NoteTypeEnum::class
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
