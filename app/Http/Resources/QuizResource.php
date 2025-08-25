<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'full_score'  => $this->full_score,
            'is_active'   => (bool) $this->is_active,
            'taken_at'    => $this->taken_at ? $this->taken_at->format('Y-m-d H:i:s') : null,
            'questions_count' => $this->questions_count ?? $this->questions()->count(),
            'student_count' => $this->scores()->count(),
            'quiz_photo'  => $this->quiz_photo
                ? asset('storage/' . $this->quiz_photo)
                : asset('storage/quiz_images/default.png'),

            // Teacher info (optional, remove if you don’t need)
            'created_by'  => [
                'id'   => auth()->user()->id ?? null,
            ],

            // Multiple targets
            'targets' => QuizTargetResource::collection($this->whenLoaded('targets')),

            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
