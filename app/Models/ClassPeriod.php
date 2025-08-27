<?php

namespace App\Models;

use App\Enums\ClassPeriodType;
use Illuminate\Database\Eloquent\Model;

class ClassPeriod extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'school_shift_id',
        'period_order',
        'type',
        'created_by',
        'duration_minutes'
    ];

    protected $casts = [
        'type' => ClassPeriodType::class,
    ];

    protected static function boot()
    {
        parent::boot();

        // Prevent deletion if there are related class sessions
        static::deleting(function ($classPeriod) {
            if ($classPeriod->classSessions()->exists()) {
                throw new \Exception('Cannot delete class period: It has related class sessions. Please delete the class sessions first or use force delete.');
            }
        });
    }

    public function schoolShift()
    {
        return $this->belongsTo(SchoolShift::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function classSessions()
    {
        return $this->hasMany(ClassSession::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the class period can be deleted
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return !$this->classSessions()->exists() && !$this->schedules()->exists();
    }

    /**
     * Get the reason why the class period cannot be deleted
     * @return string|null
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->classSessions()->exists()) {
            return 'has_class_sessions';
        }
        
        if ($this->schedules()->exists()) {
            return 'has_schedules';
        }
        
        return null;
    }

    /**
     * Force delete the class period and all related records
     * @return bool
     */
    public function forceDelete(): bool
    {
        // Delete related class sessions first
        $this->classSessions()->delete();
        
        // Delete related schedules
        $this->schedules()->delete();
        
        // Now delete the class period
        return parent::delete();
    }
}
