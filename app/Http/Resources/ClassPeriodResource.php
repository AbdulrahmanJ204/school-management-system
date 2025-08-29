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
            'period_order'     => $this->period_order,
            'duration_minutes' => $this->duration_minutes,
            'school_shift'     => $this->whenLoaded('schoolShift', function () {
                return [
                    'id'         => $this->schoolShift->id,
                    'name'       => $this->schoolShift->name,
                    'start_time' => $this->schoolShift->start_time,
                    'end_time'   => $this->schoolShift->end_time,
                ];
            }),
        ];
    }
}
