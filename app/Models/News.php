<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = ['title', 'content', 'school_day_id','photo', 'created_at', 'updated_at', 'created_by'];

    protected $casts = [
        'description' => 'json',
    ];
    public function newsTargets(): HasMany
    {
        return $this->hasMany(NewsTarget::class, 'news_id');
    }
    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }
}
