<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class YearResource extends JsonResource
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
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,

//            'created_by' => new UserResource($this->whenLoaded('createdBy')),
            'semesters' => SemesterResource::collection($this->whenLoaded('semesters')),
        ];
    }
}
