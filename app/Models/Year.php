<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Relations
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function semesters()
    {
        return $this->hasMany(Semester::class);
    }

    public function settingGradeYears()
    {
        return $this->hasMany(GradeYearSetting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

