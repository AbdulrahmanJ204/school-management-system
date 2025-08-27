<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassPeriodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'start_time'       => $this->start_time,
            'end_time'         => $this->end_time,
            'school_shift_id'  => $this->school_shift_id,
            'period_order'     => $this->period_order,
            'type'             => $this->type,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
