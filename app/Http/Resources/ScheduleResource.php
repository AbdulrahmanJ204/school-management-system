<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                         => $this->id,
            'class_period_id'            => $this->class_period_id,
            'teacher_section_subject_id' => $this->teacher_section_subject_id,
            'timetable_id'               => $this->timetable_id,
            'week_day'                   => $this->week_day,
            'created_by'                 => $this->created_by,
        ];
    }
}
