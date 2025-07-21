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
            'id'              => $this->id,
            'name'            => $this->name,
            'questions_count' => $this->questions_count,
            'taken_at'        => $this->taken_at,
            'targets'    => QuizTargetResource::collection($this->whenLoaded('targets')),
        ];
    }
}
