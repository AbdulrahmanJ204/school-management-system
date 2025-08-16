<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolShiftResource extends JsonResource
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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_active' => $this->is_active,
            'grade_ids'   => $this->targets->pluck('grade_id')->unique()->values(),
            'section_ids' => $this->targets->pluck('section_id')->unique()->values(),
        ];
    }
}
