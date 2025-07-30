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
    protected $fillable = ['title', 'content', 'publish_date','photo', 'created_at', 'updated_at', 'created_by'];

    protected $casts = [
        'description' => 'json',
        'publish_date' => 'datetime',
    ];
    protected $with = ['targets.grade' , 'targets.section.grade'];
    public function targets(): HasMany
    {
        return $this->hasMany(NewsTarget::class, 'news_id');
    }


    public function loadDeletedNewsTargets(): self
    {
        if ($this->trashed()) {
            $targets = NewsTarget::onlyTrashed()
                ->where('news_id', $this->id)
                ->where('deleted_at', $this->deleted_at)
                ->with(['section.grade', 'grade'])
                ->get();
            $this->setRelation('targets', $targets);
        }
        return $this;
    }

}
