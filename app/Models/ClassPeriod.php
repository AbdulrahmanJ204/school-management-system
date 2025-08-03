<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassPeriod extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'school_shift_id',
        'period_order',
    ];
    public function schoolShift()
    {
        return $this->belongsTo(SchoolShift::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
