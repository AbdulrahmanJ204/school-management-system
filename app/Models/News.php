<?php

namespace App\Models;

use App\Enums\StringsManager\NewsStr;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory, SoftDeletes;

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
        $clone->load('targets.section.grade', 'targets.grade');
        return $clone;
    }

    public function targets(): HasMany
    {
        return $this->hasMany(NewsTarget::class, 'news_id');
    }


    public function deletedTargets()
    {
        return $this->targets()->onlyTrashed()
            ->where('deleted_at', $this->deleted_at)
            ->with(['section.grade', 'grade'])
            ->get();
    }

    public function restoreWithTargets()
    {
        $targets = $this->deletedTargets();
        $this->restore();
        $targets->each->restore();
        $this->setRelation('targets', $targets);

        return $this;
    }

    public function loadDeletedNewsTargets(): self
    {
        if ($this->trashed()) {
            $targets = $this->deletedTargets();
            $this->setRelation('targets', $targets);
        }
        return $this;
    }

    #[Scope]
    protected function belongsToYear($query, $yearId)
    {
        $year = Year::find($yearId);
        return $query->whereBetween('publish_date', [$year->start_date, $year->end_date]);
    }

    #[Scope]
    protected function inDateRange($query, $start_date, $end_date)
    {
        return $query->whereBetween('publish_date', [$start_date, $end_date]);
    }

    #[Scope]
    protected function orderByPublishDate($query, $direction = 'desc')
    {
        return $query->orderBy('publish_date', $direction);
    }

    #[Scope]
    protected function forStudent($query, $enrollments)
    {
        return $query->where(function ($q) use ($enrollments) {
            foreach ($enrollments as $enrollment) {
                $q->orWhere(function ($subQ) use ($enrollment) {
                    $subQ->inDateRange($enrollment->semester->start_date, $enrollment->semester->end_date)
                        ->forSection($enrollment->section_id);
                });
            }
        });
    }

    #[Scope]
    protected function forSection($query, $sectionId)
    {
        return $query->whereHas('targets', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        });
    }

    #[Scope]
    protected function forGrade($query, $gradeId)
    {
        return $query->whereHas('targets', function ($q) use ($gradeId) {
            $q->where('grade_id', $gradeId);
        });
    }

    #[Scope]
    protected function forGradeOrPublic($query, $gradeId)
    {
        return $query->whereHas('targets', function ($q) use ($gradeId) {
            $q->where('grade_id', $gradeId)
                ->orWhere(function ($subQ) {
                    $subQ->whereNull('section_id')
                        ->whereNull('grade_id');
                });
        });
    }

    #[Scope]
    protected function publicNews($query)
    {
        return $query->whereHas('targets', function ($q) {
            $q->whereNull('section_id')
                ->whereNull('grade_id');
        });
    }

}
