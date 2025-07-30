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
            'created_by' => $this->created_by,
            
            // Relationships
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'enrollment' => new StudentEnrollmentResource($this->whenLoaded('enrollment')),
            'student' => new StudentResource($this->whenLoaded('enrollment.student')),
            'section' => new SectionResource($this->whenLoaded('enrollment.section')),
            'semester' => new SemesterResource($this->whenLoaded('enrollment.semester')),
            'created_by_user' => new UserResource($this->whenLoaded('createdBy')),
            
            // Computed properties
            'is_pass' => $this->isPass(),
            'grade' => $this->getGrade(),
            'percentage' => $this->getPercentage(),
            
            // Marks breakdown
            'marks_breakdown' => [
                'homework' => [
                    'mark' => $this->homework,
                    'percentage' => $this->subject ? $this->subject->homework_percentage : null,
                    'weighted_mark' => $this->homework && $this->subject ? 
                        ($this->homework * $this->subject->homework_percentage) / 100 : null
                ],
                'oral' => [
                    'mark' => $this->oral,
                    'percentage' => $this->subject ? $this->subject->oral_percentage : null,
                    'weighted_mark' => $this->oral && $this->subject ? 
                        ($this->oral * $this->subject->oral_percentage) / 100 : null
                ],
                'activity' => [
                    'mark' => $this->activity,
                    'percentage' => $this->subject ? $this->subject->activity_percentage : null,
                    'weighted_mark' => $this->activity && $this->subject ? 
                        ($this->activity * $this->subject->activity_percentage) / 100 : null
                ],
                'quiz' => [
                    'mark' => $this->quiz,
                    'percentage' => $this->subject ? $this->subject->quiz_percentage : null,
                    'weighted_mark' => $this->quiz && $this->subject ? 
                        ($this->quiz * $this->subject->quiz_percentage) / 100 : null
                ],
                'exam' => [
                    'mark' => $this->exam,
                    'percentage' => $this->subject ? $this->subject->exam_percentage : null,
                    'weighted_mark' => $this->exam && $this->subject ? 
                        ($this->exam * $this->subject->exam_percentage) / 100 : null
                ]
            ],
            
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get the grade based on total mark.
     */
    private function getGrade()
    {
        if (!$this->total) {
            return null;
        }

        if ($this->total >= 90) {
            return 'A+';
        } elseif ($this->total >= 85) {
            return 'A';
        } elseif ($this->total >= 80) {
            return 'B+';
        } elseif ($this->total >= 75) {
            return 'B';
        } elseif ($this->total >= 70) {
            return 'C+';
        } elseif ($this->total >= 65) {
            return 'C';
        } elseif ($this->total >= 60) {
            return 'D+';
        } elseif ($this->total >= 50) {
            return 'D';
        } else {
            return 'F';
        }
    }

    /**
     * Get the percentage based on total mark and subject full mark.
     */
    private function getPercentage()
    {
        if (!$this->total || !$this->subject) {
            return null;
        }

        return round(($this->total / $this->subject->full_mark) * 100, 2);
    }
} 