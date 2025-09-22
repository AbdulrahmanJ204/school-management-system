<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Observers\BehaviorNoteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([BehaviorNoteObserver::class])]
class BehaviorNote extends Model
{
    use SoftDeletes;

    protected $table = 'behavior_notes';

    protected $fillable = [
        'student_id',
        'school_day_id',
        'behavior_type',
        'note',
        'created_by'
    ];

    protected $casts = [
        'behavior_type' => 'string'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
