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
    protected $with = ['newsTargets.grade' , 'newsTargets.section.grade'];
    public function newsTargets(): HasMany
    {
        return $this->hasMany(NewsTarget::class, 'news_id');
    }
    public function schoolDay(): BelongsTo
    {
        return $this->belongsTo(SchoolDay::class, 'school_day_id');
    }

    public function loadDeletedNewsTargets(): self
    {
        if ($this->trashed()) {
            $targets = NewsTarget::onlyTrashed()
                ->where('news_id', $this->id)
                ->where('deleted_at', $this->deleted_at)
                ->with(['section.grade', 'grade'])
                ->get();
            $this->setRelation('newsTargets', $targets);
        }
        return $this;
    }

}
