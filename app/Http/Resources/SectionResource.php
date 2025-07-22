<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            'grade_id' => $this->grade_id,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
            'grade' => new GradeResource($this->whenLoaded('grade')),
//            'student_enrollments' => StudentEnrollmentResource::collection($this->whenLoaded('studentEnrollments')),
//            'teacher_section_subjects' => TeacherSectionSubjectResource::collection($this->whenLoaded('teacherSectionSubjects')),
        ];
    }
}
