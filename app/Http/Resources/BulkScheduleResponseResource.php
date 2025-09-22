<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BulkScheduleResponseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'section_id' => $this->section_id,
            'timetable_id' => $this->timetable_id,
            'schedules' => ScheduleDetailResource::collection($this->schedules),
        ];

        // Add operation counts
        if (isset($this->created_count)) {
            $data['created_count'] = $this->created_count;
        }
        if (isset($this->updated_count)) {
            $data['updated_count'] = $this->updated_count;
        }
        if (isset($this->deleted_count)) {
            $data['deleted_count'] = $this->deleted_count;
        }

        // Add operation summary
        $operations = [];
        if (isset($this->created_count) && $this->created_count > 0) {
            $operations[] = "{$this->created_count} created";
        }
        if (isset($this->updated_count) && $this->updated_count > 0) {
            $operations[] = "{$this->updated_count} updated";
        }
        if (isset($this->deleted_count) && $this->deleted_count > 0) {
            $operations[] = "{$this->deleted_count} deleted";
        }
        
        if (!empty($operations)) {
            $data['operations_summary'] = implode(', ', $operations);
        }

        return $data;
    }
}
