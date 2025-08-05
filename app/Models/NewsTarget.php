<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsTarget extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'news_id',
        'grade_id',
        'section_id',
        'created_by'
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class, 'grade_id');
    }

    #[Scope]
    protected function sectionTargets($query)
    {
        return $query->whereNotNull('section_id')
            ->whereNull('grade_id');
    }

    #[Scope]
    protected function gradeTargets($query)
    {
        return $query->whereNull('section_id')
            ->whereNotNull('grade_id');
    }

    #[Scope]
    protected function generalTargets($query)
    {
        return $query->whereNull('section_id')
            ->whereNull('grade_id');
    }


    #[Scope]
    protected function forNewsWithDeletedAt($query, $newsId, $deletedAt)
    {
        return $query->where('news_id', $newsId)
            ->where('deleted_at', $deletedAt);
    }

    #[Scope]
    protected function inSections($query, array $sectionIds)
    {
        return $query->whereIn('section_id', $sectionIds);
    }

    #[Scope]
    protected function inGrades($query, array $gradeIds)
    {
        return $query->whereIn('grade_id', $gradeIds);
    }

}
