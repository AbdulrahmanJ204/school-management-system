<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
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
            'year_id' => $this->year_id,
            'name' => $this->name,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            'year' => new YearResource($this->whenLoaded('year')),
//
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
//            'created_by' => new UserResource($this->whenLoaded('createdBy')),

            'school_days' => SchoolDayResource::collection($this->whenLoaded('schoolDays')),
            'school_days_count' => $this->whenCounted('schoolDays'),

            'study_days_count' => $this->when($this->relationLoaded('schoolDays'), function() {
                return $this->schoolDays->where('type', 'study')->count();
            }),
            'exam_days_count' => $this->when($this->relationLoaded('schoolDays'), function() {
                return $this->schoolDays->where('type', 'exam')->count();
            }),

        ];
    }
}
