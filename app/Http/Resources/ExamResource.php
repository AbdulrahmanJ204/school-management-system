<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
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
            'school_day_id' => $this->school_day_id,
            'grade_id' => $this->grade_id,
            'main_subject_id' => $this->main_subject_id,
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,

            // Relationships
            'school_day' => new SchoolDayResource($this->whenLoaded('schoolDay')),
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'main_subject' => new MainSubjectResource($this->whenLoaded('mainSubject')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
} 