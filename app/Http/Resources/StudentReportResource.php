<?php

namespace App\Http\Resources;

use App\Http\Resources\Basic\SectionBasicResource;
use App\Http\Resources\Basic\GradeBasicResource;
use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

/**
 * Student Report Resource - Comprehensive student academic report
 * مورد تقرير الطالب - تقرير أكاديمي شامل للطالب
 */
class StudentReportResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'student' => [
                'id' => $this->resource['student']->id,
                'name' => $this->resource['student']->user->first_name . ' ' . $this->resource['student']->user->last_name,
                'section' => $this->resource['section']->title,
                'grade' => $this->resource['grade']->title
            ],
            'semester' => [
                'id' => $this->resource['semester']->id,
                'name' => $this->resource['semester']->name
            ],
            'main_subjects' => $this->resource['mainSubjects'],
            'overall_performance' => $this->resource['overallPerformance']
        ];
    }
}
