<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'created_by'
    ];

    public function user(): BelongsTo
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function teacherSectionSubjects()
    {
        return $this->hasMany(TeacherSectionSubject::class);
    }
    public function createdBy()
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
