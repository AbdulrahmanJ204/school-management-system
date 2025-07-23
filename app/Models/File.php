<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected  $fillable = [
        'subject_id',
        'title',
        'description',
        'size',
        'file',
        'school_day_id',
        'created_by',
    ];
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class);
    }
    public function targets(): HasMany{
        return $this->hasMany(FileTarget::class);
    }
}
