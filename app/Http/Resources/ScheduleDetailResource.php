<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleDetailResource extends JsonResource
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
            'class_period_id' => $this->class_period_id,
            'class_period_name' => $this->classPeriod->name,
            'class_period_time' => $this->classPeriod->start_time . ' - ' . $this->classPeriod->end_time,
            'teacher_section_subject_id' => $this->teacher_section_subject_id,
            'teacher_name' => $this->teacherSectionSubject->teacher->user->name ?? 'N/A',
            'subject_name' => $this->teacherSectionSubject->subject->name ?? 'N/A',
            'subject_code' => $this->teacherSectionSubject->subject->code ?? 'N/A',
            'week_day' => $this->week_day,
            'created_at' => $this->created_at,
        ];
    }
}
