<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolShift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'created_by'
    ];
    public function classPeriods()
    {
        return $this->hasMany(ClassPeriod::class);
    }
    public function targets()
    {
        return $this->hasMany(SchoolShiftTarget::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
