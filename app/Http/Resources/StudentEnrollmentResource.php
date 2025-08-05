<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
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
            'student_id' => $this->student_id,
            'section_id' => $this->section_id,
            'semester_id' => $this->semester_id,
            'enrollment_date' => $this->enrollment_date,
            'status' => $this->status,
            'created_by' => $this->created_by,

            // Relationships
            // 'student' => new StudentResource($this->whenLoaded('student')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'semester' => new SemesterResource($this->whenLoaded('semester')),
            'created_by_user' => new UserResource($this->whenLoaded('createdBy')),

            // Computed properties
            'grade' => new GradeResource($this->whenLoaded('section.grade')),
            'year' => new YearResource($this->whenLoaded('semester.year')),
            'user' => new UserResource($this->whenLoaded('student.user')),

            // Student marks summary
//            'student_marks' => StudentMarkResource::collection($this->whenLoaded('studentMarks')),
//            'marks_summary' => [
//                'total_marks' => $this->getTotalMarks(),
//                'average_marks' => $this->getAverageMarks(),
//                'failed_subjects_count' => $this->getFailedSubjects()->count(),
//                'is_promoted' => $this->isPromoted(),
//            ],

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
