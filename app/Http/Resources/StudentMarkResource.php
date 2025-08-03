<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentMarkResource extends JsonResource
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
            'subject_id' => $this->subject_id,
            'enrollment_id' => $this->enrollment_id,
            'homework' => $this->homework,
            'oral' => $this->oral,
            'activity' => $this->activity,
            'quiz' => $this->quiz,
            'exam' => $this->exam,
            'total' => $this->total,
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,

            // Relationships
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'enrollment' => new StudentEnrollmentResource($this->whenLoaded('enrollment')),
            // 'student' => new StudentResource($this->whenLoaded('enrollment.student')),
            'section' => new SectionResource($this->whenLoaded('enrollment.section')),
            'semester' => new SemesterResource($this->whenLoaded('enrollment.semester')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
