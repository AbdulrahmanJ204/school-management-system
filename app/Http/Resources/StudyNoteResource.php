<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyNoteResource extends JsonResource
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
            'school_day_id' => $this->school_day_id,
            'date' => $this->schoolDay->date?->format('Y-m-d'),
            'subject_id' => $this->subject_id,
            'note_type' => $this->note_type,
            'note' => $this->note,
            'student_mark' => $this->marks,
            'created_by' => $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
            'sender_name' => $this->createdBy->first_name . ' ' . $this->createdBy->last_name,

            // Relationships
            // 'student' => new StudentResource($this->whenLoaded('student')),
            'school_day' => new SchoolDayResource($this->whenLoaded('schoolDay')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
