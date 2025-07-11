<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectMajor extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id',
        'name',
        'code',
        'success_rate',
        'created_by'
    ];

    protected $casts = [
        'success_rate' => 'integer'
    ];

    // Relations
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}

