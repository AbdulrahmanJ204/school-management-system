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
            'id'              => $this->id,
            'title'           => $this->name,
            'question_count'  => $this->questions->count(),
            'student_count'   => $this->scores()->count(),
            'date'            => $this->created_at->format('Y-m-d'),
            'full_score'      => $this->full_score,
            'targets'         => QuizTargetResource::collection($this->targets),
            'questions'       => QuestionResource::collection($this->questions),
            'created_by' => auth()->user()->id,
        ];
    }
}
