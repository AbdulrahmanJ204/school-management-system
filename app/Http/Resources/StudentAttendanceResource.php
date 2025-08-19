<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
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
            'student' => [
                'id' => $this->student->id,
                'name' => $this->student->user->first_name . ' ' . $this->student->user->father_name . ' ' . $this->student->user->last_name,
                'student_id' => $this->student->student_id,
            ],
            'class_session' => [
                'id' => $this->classSession->id,
                'date' => $this->classSession->date->format('Y-m-d'),
                'time' => $this->classSession->time->format('H:i'),
            ],
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'created_by' => [
                'id' => $this->createdBy->id,
                'name' => $this->createdBy->first_name . ' ' . $this->createdBy->father_name . ' ' . $this->createdBy->last_name,
            ],
        ];
    }
}
