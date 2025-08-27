<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'section_id' => $this->section_id,
            'timetable_id' => $this->timetable_id,
            'schedules' => ScheduleDetailResource::collection($this->schedules),
            'week_days' => $this->week_days,
            'class_periods' => ClassPeriodResource::collection($this->class_periods),
        ];
    }
}
