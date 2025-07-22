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
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
            'sections' => SectionResource::collection($this->whenLoaded('sections')),
//            'subject_majors' => SubjectMajorResource::collection($this->whenLoaded('subjectMajors')),
//            'setting_grade_years' => SettingGradeYearResource::collection($this->whenLoaded('settingGradeYears')),
        ];
    }
}
