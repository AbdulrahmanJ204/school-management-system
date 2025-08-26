<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolShift extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $with = [ 'targets'];

    protected static function boot(): void
    {
        parent::boot();
        static::deleting(function ($schoolShift) {
            $schoolShift->targets()->delete();
        });
    }
    public function classPeriods(): HasMany
    {
        return $this->hasMany(ClassPeriod::class);
    }
    public function targets(): HasMany
    {
        return $this->hasMany(SchoolShiftTarget::class, 'school_shift_id');
    }
    public function allTargets(): HasMany{
        return $this->hasMany(SchoolShiftTarget::class, 'school_shift_id');
    }

    public function loadTargets(): void
    {
        $this->setRelation('targets', $this->targets()->get());
    }
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if the school shift can be deleted
     * @return bool
     */
    public function canBeDeleted(): bool
    {
        return !$this->classPeriods()->exists();
    }

    /**
     * Get the reason why the school shift cannot be deleted
     * @return string|null
     */
    public function getDeletionBlockReason(): ?string
    {
        if ($this->classPeriods()->exists()) {
            return 'has_class_periods';
        }
        
        return null;
    }

    /**
     * Get detailed information about what's preventing deletion
     * @return array
     */
    public function getDeletionBlockDetails(): array
    {
        $details = [];
        
        $classPeriodsCount = $this->classPeriods()->count();
        if ($classPeriodsCount > 0) {
            $details['class_periods'] = $classPeriodsCount;
        }
        
        return $details;
    }
}
