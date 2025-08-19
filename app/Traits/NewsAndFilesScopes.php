<?php

namespace App\Traits;

use App\Models\Year;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait NewsAndFilesScopes
{

    #[Scope]
    protected function belongsToYear($query, $yearId)
    {
        $year = Year::select(['start_date', 'end_date'])->findOrFail($yearId);

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
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';

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
                    $subQ->generalTargets();
                });
        });
    }
    #[Scope]
    protected function forSectionOrPublic($query, $sectionId)
    {
        return $query->whereHas('targets', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId)
                ->orWhere(function ($subQ) {
                    $subQ->generalTargets();
                });
        });
    }

    #[Scope]
    protected function forPublic($query)
    {
        return $query->whereHas('targets', function ($q) {
            $q->generalTargets();
        });
    }
}
