<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeYearSettingResource extends JsonResource
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
            'year_id' => $this->year_id,
            'grade_id' => $this->grade_id,
            'max_failed_subjects' => $this->max_failed_subjects,
            'help_marks' => $this->help_marks,

            // Relationships
            'year' => new YearResource($this->whenLoaded('year')),
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
