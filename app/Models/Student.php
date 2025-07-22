<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'grandfather',
        'general_id',
        'created_by',
        'is_active',
        'mother'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by'); // Creator admin
    }
    public function studentEnrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id');
    }
}
