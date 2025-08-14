<?php

namespace App\Traits;

use App\Models\Year;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait TargetsScopes
{


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
