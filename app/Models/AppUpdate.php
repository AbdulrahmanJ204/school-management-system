<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppUpdate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'version',
        'platform',
        'url',
        'change_log',
        'is_force_update',
        'created_by'
    ];

    protected $casts = [
        'is_force_update' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user who created this app update
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by platform
     */
    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    /**
     * Scope to get the latest version for a platform
     */
    public function scopeLatestForPlatform($query, $platform)
    {
        return $query->byPlatform($platform)
                    ->orderByRaw('CAST(SUBSTRING_INDEX(version, ".", 1) AS UNSIGNED) DESC, 
                                  CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(version, ".", 2), ".", -1) AS UNSIGNED) DESC,
                                  CAST(SUBSTRING_INDEX(version, ".", -1) AS UNSIGNED) DESC')
                    ->limit(1);
    }
}
