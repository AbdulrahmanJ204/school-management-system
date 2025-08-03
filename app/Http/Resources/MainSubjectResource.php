<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainSubjectResource extends JsonResource
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
            'code' => $this->code,
            'success_rate' => $this->success_rate,
            'grade_id' => $this->grade_id,
            'grade' => new GradeResource($this->whenLoaded('grade')),
            'created_by' => $this->createdBy->id . '-' . $this->createdBy->first_name . ' ' . $this->createdBy->last_name,
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
            'subjects_count' => $this->when(
                $this->relationLoaded('subjects'),
                $this->subjects->count()
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
