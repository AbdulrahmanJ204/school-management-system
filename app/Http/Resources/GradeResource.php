<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year_id' => $this->year_id,
            'year' => $this->whenLoaded('year', function () {
                return [
                    'id' => $this->year->id,
                    'name' => $this->year->name,
                    'start_date' => $this->year->start_date?->format('Y-m-d'),
                    'end_date' => $this->year->end_date?->format('Y-m-d'),
                    'is_active' => $this->year->is_active,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
            'main_subjects' => MainSubjectResource::collection($this->whenLoaded('mainSubjects')),
            // 'setting_grade_years' => SettingGradeYearResource::collection($this->whesnLoaded('settingGradeYears')),

        ];
    }
}
