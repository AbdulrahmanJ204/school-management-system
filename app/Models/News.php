<?php

namespace App\Models;

use App\Enums\StringsManager\NewsStr;
use App\Traits\NewsAndFilesScopes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory, SoftDeletes ,NewsAndFilesScopes;

    protected $fillable = ['title', 'content', 'publish_date', 'photo', 'created_at', 'updated_at', 'created_by'];

    protected $casts = [
        'description' => 'json',
        'publish_date' => 'datetime',
    ];
    protected $with = ['targets.grade', 'targets.section.grade'];

    protected static function boot()
    {
        parent::boot();
        // TODO: May move these to observer
        static::deleting(function ($news) {
            if (!$news->isForceDeleting()) {
                $news->targets()->delete();
            }
        });

        static::forceDeleting(function ($news) {
            if ($news->photo) {
                Storage::disk(NewsStr::StorageDisk->value)->delete($news->photo);
            }
            $news->targets()->withTrashed()->forceDelete();
        });
    }

    public function getDeleteSnapshot(): self
    {
        $clone = clone $this;
        $targets = $this->targets()->get();
        $clone->setRelation('targets', $targets);
        return $clone;
    }

    public function targets(): HasMany
    {
        return $this->hasMany(NewsTarget::class, 'news_id')
            ->withTrashed()
            ->where('deleted_at', $this->deleted_at);
    }


    public function restoreWithTargets(): static
    {
        $targets = $this->targets()->get();

        $this->restore();
        $targets->each->restore();

        $this->setRelation('targets', $targets);
        return $this;
    }
}
