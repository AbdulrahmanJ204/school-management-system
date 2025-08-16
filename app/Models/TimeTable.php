<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    protected $fillable = [
        'valid_from',
        'valid_to',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to'   => 'date',
        'is_active'  => 'boolean',
    ];
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
