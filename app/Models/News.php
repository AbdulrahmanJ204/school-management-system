<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    //
    protected $fillable = ['title', 'content', 'school_day_id', 'created_at', 'updated_at', 'created_by'];

    public function newsTargets(){
        return $this->hasMany(NewsTarget::class, 'news_id');
    }
    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }
}
