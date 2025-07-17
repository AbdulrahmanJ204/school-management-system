<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailedQuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'           => $this->name,
            'question_count'  => $this->questions->count(),
            'date'            => $this->created_at->format('Y-m-d'),
            'targets'         => QuizTargetResource::collection($this->targets),
            'questions'       => QuestionResource::collection($this->questions),
        ];
    }
}
