<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'subject_name' => $this->teacherSectionSubject->subject->name,
            'teacher_name' => $this->teacherSectionSubject->teacher->name,
            'start_time'   => $this->classPeriod->start_time,
            'end_time'     => $this->classPeriod->end_time,
            'section_name' => $this->teacherSectionSubject->section->name,
            'grade_name'   => $this->teacherSectionSubject->section->grade->name,
        ];
    }
}
