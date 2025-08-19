<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'file',
        'line',
        'message',
        'trace',
        'url',
        'method',
        'input',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'input' => 'array',
    ];

    /**
     * Get the user that encountered the error.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by error code.
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Scope to filter by file.
     */
    public function scopeByFile($query, $file)
    {
        return $query->where('file', $file);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }
}
