<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'full_mark' => $this->full_mark,
            'homework_percentage' => $this->homework_percentage,
            'oral_percentage' => $this->oral_percentage,
            'activity_percentage' => $this->activity_percentage,
            'quiz_percentage' => $this->quiz_percentage,
            'exam_percentage' => $this->exam_percentage,
            'num_class_period' => $this->num_class_period,
            'main_subject' => new MainSubjectResource($this->whenLoaded('mainSubject')),
            'grade' => new GradeResource($this->whenLoaded('mainSubject.grade')),
            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'percentages_summary' => [
                'homework' => $this->homework_percentage . '%',
                'oral' => $this->oral_percentage . '%',
                'activity' => $this->activity_percentage . '%',
                'quiz' => $this->quiz_percentage . '%',
                'exam' => $this->exam_percentage . '%',
                'total' => ($this->homework_percentage + $this->oral_percentage +
                        $this->activity_percentage + $this->quiz_percentage +
                        $this->exam_percentage) . '%'
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
